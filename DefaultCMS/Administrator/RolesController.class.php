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
	function specialPermissions (){
		$cons = get_subclasses ('Component');
		$arr = array();
		/*foreach($cons as $c) {
			$con = new $c($v=&new ValueHolder(''));
			$p = $con->permissionNeeded();
			if (is_array($p)){
				foreach($p as $perm)
				$arr[$perm] = $perm;
			} else {
				$arr [$p]= $p;
			}
		}*/
		return $arr;
	}
	function initialize() {
		$this->addComponent(new Label(''),'status');
		$s =& new Select(new ValueHolder($v2=0),new PersistentCollection(Role));
		$s->addEventListener(array('changed'=>'setRole'), $this);
		$this->addComponent($s, 'roles');
		$this->setRole();
	}
	function addCheckBox($perm){
		$fc =& new Component;
		$cb =& new CheckBox(new ValueHolder($this->role->havePermission($perm)));
		$cb->addEventListener(array('changed'=>'changePermission'),$this);
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
		$db =& DBSession::instance();
		$ok = $db->query($sql);
		if ($ok){
			$this->status->setValue('changed permission '.$perm);
		} else {
			$this->status->setValue(DBSession::lastError());
		}

	}
	function setRole(){
		$this->role =& $this->roles->getValue();
		if (getClass($this->role)=='role'){
			$acts =& $this->actions();
			$self =& $this;
			$this->addComponent(new Component, 'perms');
			foreach(get_subclasses('PersistentObject') as $name) {
				 $acts->map($f = lambda('$act','$self->addCheckBox($name."=>".$act);',get_defined_vars()));
			}
			delete_lambda($f);
		}
		foreach($this->specialPermissions() as $perm){
			$this->addCheckBox($perm);
		}
	}
}

?>