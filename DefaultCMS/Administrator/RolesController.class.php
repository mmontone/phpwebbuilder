<?php

/**
 * Controller for editing the Permissions for each role.
 *
 */

class RolesController extends Component {
	var $role;
	function permissionNeeded () {
		return "UserAdmin";
	}
	function actions (){
		$col =& new Collection;
		$col->addAll(array("Add", "Edit", "Show", "Delete", "List", "*", "Menu"));
		return $col;
	}
	function specialPermissions ($form){
		$cons = get_subclasses ('Component');
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
	function initialize() {
		$this->addComponent(new Label('aaa'),'status');
		$s =& new Select(new ValueHolder($v2=0),new PersistentCollection(Role));
		$s->addEventListener(array('change'=>'setRole'), $this);
		$this->addComponent($s, 'roles');

		foreach($this->actions() as $act){
			$ret .="<td>$act</td>";
		}
		$this->setRole();
	}
	function addCheckBox($perm){
		$fc =& new Component;
		$cb =& new CheckBox(new ValueHolder($this->role->havePermission($perm)));
		$cb->addEventListener(array('change'=>'changePermission'),$this);
		$fc->addComponent(new Label($perm), 'name');
		$fc->addComponent($cb, 'val');
		$this->perms->addComponent($fc);
	}
	function changePermission(&$cb){
		$perm = $cb->holder->parent->name->getValue();
		if ($cb->getValue()){
			$sql = "INSERT INTO RolePermission (permission,role)VALUES ('$perm', ".$this->role->getId().")";
		} else {
			$sql= "DELETE FROM RolePermission WHERE permission='$perm' AND role=".$this->role->getId();
		}
		$db =& DB::instance();
		$ok = $db->query($sql);
		if ($ok){
			$this->status->setValue('changed permission '.$perm);
		} else {
			$this->status->setValue(DB::lastError());
		}

	}
	function setRole(){
		$this->role =& $this->roles->getValue();
		if (getClass($this->role)=='role'){
			$acts =& $this->actions();
			$self =& $this;
			$this->addComponent(new Component, 'perms');
			foreach(get_subclasses('PersistentObject') as $name) {
				 $acts->map(lambda('$act','$self->addCheckBox($name."=>".$act);',get_defined_vars()));
			}
		}
/*		foreach($this->specialPermissions() as $perm){
				$ret .="<td><input type=\"checkbox\" name=\"".$perm."\"";
				if ($role->havePermission($perm)) $ret .=	"checked=\"checked\"";
				$ret .=	" value=\"Insert\">$perm</td>";
		}
		$ret .=		"<input name=\"execform\" type=\"submit\"></form>";*/
	}
}

?>