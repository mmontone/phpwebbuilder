<?

class ModUser extends Component {
	function hasPermission(){
		return !User::is_guest();
	}
	function createInstance(){
		$u =& User::logged();
		$fs = $u->allFieldNames();
		unset($fs['UserRoleuser']);
		unset($fs['super']);
		unset($fs['id']);
		$this->addComponent(new PersistentObjectEditor($u,$fs), 'edit');
		$this->edit->registerCallback('object_edited', new FunctionObject($u, 'save'));
	}
}
?>
