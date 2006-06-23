<?

class ModUser extends PersistentObjectEditor {
	function hasPermission(){
		return !User::is_guest();
	}
	function ModUser(){
		$u =& User::logged();
		$fs = $u->allFieldNames();
		unset($fs['UserRoleuser']);
		unset($fs['super']);
		unset($fs['id']);
		parent::PersistentObjectEditor($u,$fs);
	}
}
?>
