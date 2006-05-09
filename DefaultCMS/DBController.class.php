<?php
/**
 * This controller helps configuring and checking the database.
 */

require_once pwbdir.'/OldViews/Structure/TableCheckView.class.php';

class DBController extends Component {
	var $sql;
	var $sql_result;
	function initialize(){
		$this->sql =& new ValueHolder($n='');
		$this->sql_result =& new ValueHolder($n2='');
		$this->add_component(new Label('Execute SQL in Database:'));
		$this->add_component(new TextAreaComponent($this->sql), 'sql_comp');
		$this->add_component(new Text($this->sql_result), 'sql_res');
		$this->add_component(new ActionLink($this, 'exec_sql', 'Execute the SQL', $n3=null), 'exec_sql');

		$this->add_component(new Label('Check Table Structure'));
		$this->add_component(new ActionLink($this, 'check_tables', 'Check Table Structure', $n=null), 'check_tables');
	}
	function permissionNeeded () {
		return "DatabaseAdmin";
	}
	function exec_sql() {
		$db = new MySQLDB;
		$sqlstr = $this->sql->getValue();
		$sqls = split(";",$sqlstr);
		$ress = $db->batchExec($sqls);
		$this->sql_result->setValue(print_r($ress, TRUE));
	}
	function check_tables() {
		$mod = $this->modsNeeded();
		if ($mod!="") {
			$this->sql->setValue($mod);
			$this->sql_result->setValue($n = "Modifications Needed");
		} else {
			$this->sql_result->setValue($n = "No problems found!");
		}
	}
	function modsNeeded(){
		$arr = get_subclasses("PersistentObject");
		/*Comparing existing tables, existing objects, and added objects*/
		/*If a table has not an object table, we have to delete it*/
		foreach ($arr as $o) {
			$obj = new $o;
			$dbc = new PersistentObjectTableCheckView;
			$dbc->obj=$obj;
			$mod .= $dbc->show();
		}
		return $mod;
	}
}
?>