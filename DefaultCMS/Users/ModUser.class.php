<?

class ModUser extends EditObjectComponent{
	function hasPermission(){
		return $_SESSION[sitename]['Username']!='guest';
	}
	function ModUser(){
		$u =& $_SESSION[sitename]['User'];
		$fs = $u->allFieldNames();
		unset($fs['UserRoleuser']);
		unset($fs['super']);
		unset($fs['id']);
		parent::EditObjectComponent($u,$fs);
	}
}
?>
