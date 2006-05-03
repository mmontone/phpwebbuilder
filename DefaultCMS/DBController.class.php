<?php
/**
 * This controller helps configuring and checking the database.
 */


class DBController extends Component {
	function permissionNeeded () {
		return "DatabaseAdmin";
	}
	function begin ($form) {
		$ret = $this->checkConfig();
		$ret .= $this->executeSQL($form);
		$ret .= $this->export();
		if (!isset($form["nocheck"])) $ret .= $this->checkTables();
		return $ret;
	}
	function checkConfig() {
/*
 * Chequear si la DB est� bien configurada (se puede acceder),
 */
		return "<h3>Check access to database:</h3> Feature not yet implemented</p>";
	}
 /* Exportar e importar, tablas y datos.*/
	function executeSQL($form) {
		$ret ="";
		if ($form["executeStage"]=="Execute"){
			$db = new MySQLDB;
			$sqlstr = stripslashes($form["execSQL"]);
			//$sqlstr = ereg_replace("--.*\n","",$sqlstr);
			$sqls = split(";",$sqlstr);
			$ress = $db->batchExec($sqls);
			$ret .= print_r($ress, TRUE);
		}
		$ret = "<h3>Execute SQL in Database:</h3> <p>" . $ret .
				"<form action=\"Action.php?Controller=DBController";
		$ret .= "\" method=\"POST\">" .
				"<textarea rows=\"20\" cols=\"100\" name=\"execSQL\">$sqlstr</textarea>" .
				"<input type=\"submit\" name=\"executeStage\" value=\"Execute\" />" .
				"</form>";
		return $ret;
	}
	function export() {
		return "<h3>Export data:</h3> <p>Feature not yet implemented</p>";
	}
	function checkTables() {
		$ret = "<h3>Check if existing tables are correct</h3>";
		$mod = $this->modsNeeded();
		if ($mod!="") {
			$ret .= "Modifications Needed: <textarea rows=\"10\" cols=\"60\">$mod</textarea>";
		} else {
			$ret .= "No problems found!";
		}
		return $ret;
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