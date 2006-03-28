<?php

require_once("Controller.class.php");
require_once pwbdir."/OldViews/Structure/HtmlTableEditView.class.php";
require_once pwbdir."/OldViews/Links/NoLinker.class.php";

/**
 * Controller for editing the Permissions for each role.
 * 
 */

class RolesController extends Controller {
	var $roleid = 1;
	function permissionNeeded () {
		return "UserAdmin";
	}
	function actions (){
	return array("Add", "Edit", "Show", "Delete", "List", "*", "Menu");
	
	}
	function specialPermissions ($form){
		$cons = get_subclasses ("Controller");
		$arr = array();
		foreach($cons as $c) {
			$con = new $c;
			$p = $con->permissionNeeded($form);
			if (is_array($p)){
				foreach($p as $perm)
				$arr[$perm] = $perm;
			} else {
				$arr [$p]= $p;
			}
		}
		return $arr;
	}
	function begin($form) {
		$ret ="";
		$ret .=$this->saveValues($form);
		$ret .=$this->showOptions($form);
		return $ret;
	}
	function saveValues($form) {
		$db = new MySQLDB;
		$sqls = array();
		if (isset($form["execform"])){
			$this->roleid=$form["roleidold"];
			foreach(get_subclasses('PersistentObject') as $name) {
				foreach($this->actions() as $act){
					if ($form["$name=>$act"]=="Insert") {						
						$sqls[] = "INSERT INTO RolePermission (permission,role)VALUES ('$name=>$act', ".$this->roleid.")";
					} else {
						$sqls []= "DELETE FROM RolePermission WHERE permission='$name=>$act' AND role=".$this->roleid;
					}
				}
			}
			foreach($this->specialPermissions($form) as $perm){
				if ($form[$perm]=="Insert") {						
					$sqls[] = "INSERT INTO RolePermission (permission,role)VALUES ('$perm', ".$this->roleid.")";
				} else {
					$sqls []= "DELETE FROM RolePermission WHERE permission='$perm' AND role=".$this->roleid;
				}
			}


			$db->batchExec($sqls);
			$this->roleid=$form["roleidnext"];
		}
	
		return "";
	}
	function showOptions($form) {
		$role = new Role;
		$role->setID($this->roleid);
		$role->load();
		$ret = "";
		$ret .="<form action=\"Action.php?Controller=RolesController";
		$ret .=		"\" method=\"POST\">" .
				"<input type=\"hidden\" name=\"roleidold\" value=\"".$this->roleid."\" />Editing Role " . $role->name->value;
		$rolecol = new PersistentCollection(Role);
		$view = new HTMLTableEditView;
		$view = $view->viewFor($rolecol);	
		$ret .=	$view->asSelect (new NoLinker(), "roleidnext", $this->roleid, "");
			
		$ret .=		"<table><tr><td>&nbsp</td>";
		foreach($this->actions() as $act){
			$ret .="<td>$act</td>";
		}
		$ret .="<tr>";
		foreach(get_subclasses('PersistentObject') as $name) {
			$ret .="<tr><td>$name</td>";
			foreach($this->actions() as $act){						
				$ret .="<td><input type=\"checkbox\" name=\"".$name ."=>". $act."\"";
				if ($role->havePermission($name."=>".$act)) $ret .=	"checked=\"checked\"";
				$ret .=	" value=\"Insert\"></td>";
			}
			$ret .="</tr>";
		}
		$ret .="</table>";
		foreach($this->specialPermissions($form) as $perm){
				$ret .="<td><input type=\"checkbox\" name=\"".$perm."\"";
				if ($role->havePermission($perm)) $ret .=	"checked=\"checked\"";
				$ret .=	" value=\"Insert\">$perm</td>";
		}
		$ret .=		"<input name=\"execform\" type=\"submit\"></form>";
		return $ret;
	}
}

?>