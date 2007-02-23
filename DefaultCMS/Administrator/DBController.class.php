<?php
/**
 * This controller helps configuring and checking the database.
 */


class DBController extends Component {
	var $sql;
	var $sql_result;
	var $new_version;
	var $dbinfo;
	var $vnum_aspect;
	var $selected_version;

	function initialize(){
		$this->dbinfo =& $this->getDBInfo();
		$this->new_version =& new DBVersion;

		$next_versions =& new PersistentCollection('DBVersion');
		$next_versions->setCondition('version', '>', $this->getCurrentVersionNumber());

		$this->vnum_aspect =& new AspectAdaptor($this, 'CurrentVersionNumber');
		$this->addComponent(new Text($this->vnum_aspect), 'version');

		if (!$next_versions->isEmpty()) {
			$this->selected_version =& new ObjectHolder($next_versions->first());
			$this->addComponent(new Label('Switch to version:'), 'switch_label');
			$this->addComponent(new Select($this->selected_version, $next_versions), 'select_version');
			$this->addComponent(new CommandLink(array('text' => 'Switch version', 'proceedFunction' => new FunctionObject($this, 'switchVersion')), $next_versions), 'switch_version');
		}
		else {
			$this->showDBUpdater();
		}
	}

	function showDBUpdater() {
		$this->sql =& new ValueHolder($n='');
		$this->sql_result =& new ValueHolder($n2='');
		$this->addComponent(new Label('Execute SQL in Database:'));
		$this->addComponent(new TextAreaComponent($this->sql), 'sql_comp');
		$this->addComponent(new Text($this->sql_result), 'sql_res');
		$this->addComponent(new ActionLink($this, 'exec_sql', 'Execute the SQL', $n3=null), 'exec_sql');
		$this->addComponent(new ActionLink($this, 'exec_oql', 'Execute in OQL', $n3=null), 'exec_oql');
		$this->addComponent(new ActionLink($this, 'exec_update', 'Update the Database', $n3=null), 'exec_update');

		$this->addComponent(new Label('Check Table Structure'));
		$this->addComponent(new Label('Step by step checking'));
		$this->addComponent(new CheckBox(new ValueHolder($n=false)), 'stepping');
		$this->addComponent(new ActionLink($this, 'check_tables', 'Check Table Structure', $n=null), 'check_tables');

		$this->addComponent(new TextAreaComponent(new AspectAdaptor($this,'MigrationCode')), 'migration_code_area');
	}

	function switchVersion() {
		$ok = $this->executeMigrationCode();
		$db =& DBSession::Instance();
		$db->beginTransaction();

		if (!$ok) {
			$db->rollback();
		}
		else {
			$this->dbinfo->version->setTarget($this->selected_version->getValue());
			$ok = $db->save($this->dbinfo);
			if (!$ok) {
				$db->rollback();
			}
			else {
				$db->commit();

				$next_versions =& new PersistentCollection('DBVersion');
				$next_versions->setCondition('version', '>', $this->getCurrentVersionNumber());

				if ($next_versions->isEmpty()) {
					$this->deleteComponentAt('switch_label');
					$this->deleteComponentAt('select_version');
					$this->deleteComponentAt('switch_version');
				}
				$this->initialize();
			}
		}
	}

	function executeMigrationCode() {
		$selected_version =& $this->selected_version->getValue();

		$vs =& new PersistentCollection('DBVersion');
		$vss =& new CompositeReport($vs);
		$vss->setCondition('version', '>', $this->getCurrentVersionNumber());
		$vss->setCondition('version', '<=', $selected_version->version->getValue());

		$code = '';
		/*
		$vss->map(lambda('$v', 'echo "Version code: " . $v->migration_code->getValue(); echo "Version: " . $v->version->getValue(); $code .= $v->migration_code->getValue();', get_defined_vars()));
		*/
		$versions =& $vss->elements();
		foreach(array_keys($versions) as $v) {
			$vv =& $versions[$v];
			$code .= $vv->migration_code->getValue();
		}
		$db =& DBSession::Instance();
		$ok = true;

		echo 'Executing code: ' . $code;
		eval($code);

		return $ok;
	}

	function getMigrationCode() {
		return $this->new_version->migration_code->getValue();
	}

	function setMigrationCode($code) {
		$this->new_version->migration_code->setValue($code);
	}

	function getCurrentVersionNumber() {
		if(!$this->dbinfo->version)	return '0';
		$current_version =& $this->dbinfo->version->getTarget();
		if (!$current_version) return '0';
		return $current_version->version->getValue();
	}

	function &getDBInfo() {
		$dbinfos =& new PersistentCollection('DBInfo');
		$dbinfo =& $dbinfos->first();
		if ($dbinfo==null) {
			$dbinfo =& new DBInfo;
			$ver = & new DBVersion;
			$ver->version->setValue(0);
			$ver->save();
			$dbinfo->version->setTarget($ver);
			$dbinfo->save();
		}
		return $dbinfo;
	}

	function permissionNeeded () {
		return "DatabaseAdmin";
	}
	function exec_oql(){
		$oql = stripslashes($this->sql->getValue());
		if (strcasecmp(substr($oql, 0,6), 'select')==0) {$oql = substr($oql,6);}
		$oq =& new OQLCompiler;
		$repstr = $oq->fromQuery($oql);
		eval('$rep =& '.$repstr.';');
		$sql = $rep->selectSql();
		$this->do_sql($sql);
	}
	function exec_sql() {
		$this->do_sql(stripslashes($this->sql->getValue()));
	}
	function exec_update() {
		$db =& DBSession::Instance();
		$this->exec_sql();

		$this->new_version->version->setValue($this->getCurrentVersionNumber() + 1);
		$this->new_version->sql->setValue($this->sql->getValue());

		$db->beginTransaction();
		$ok = $db->save($this->new_version);
		if (!$ok) {
			$db->rollback();
		}
		else {
			$this->dbinfo->version->setTarget($this->new_version);
			$result =& $db->save($this->dbinfo);
			if (is_exception($result)) {
				$db->rollback();
			}
			else {
				$db->commit();
				$this->new_version =& new DBVersion;
			}
		}
		$this->vnum_aspect->changed();
	}
	function do_sql($sqlstr){
		$db =& DBSession::Instance();
		$sqls = explode(";",$sqlstr);
		$ress = $db->batchExec($sqls);
		$this->sql_result->setValue(print_r($ress, TRUE));
	}

	function check_tables() {
		$mod = $this->modsNeeded();
		if ($mod!="") {
			$this->sql->setValue($mod);
			$this->sql_result->setValue($n = "Modifications Needed");
		} else {
			$this->sql->setValue($s="");
			$this->sql_result->setValue($n = "No problems found!");
		}
	}
	function modsNeeded(){
		return TablesChecker::checkTables($this->stepping->getValue());
	}
}
?>
