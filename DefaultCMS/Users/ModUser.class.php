<?

class ModUser extends PersistentObjectEditor {
	function hasPermission(){
		return $_SESSION[sitename]['Username']!='guest';
	}
	function ModUser(){
		$u =& $_SESSION[sitename]['User'];
		$fs =& $u->allFields();
		unset($fs['UserRoleuser']);
		unset($fs['super']);
		unset($fs['id']);
		parent::PersistentObjectEditor($u,$fs);
	}
}
?>
