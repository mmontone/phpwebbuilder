<?
class Logout extends Controller{
	function setForm($form){
		User::login('guest','guest');
		$this->triggerEvent('menuChanged');
	}
}
?>
