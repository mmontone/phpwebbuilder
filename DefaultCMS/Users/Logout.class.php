<?

class Logout extends Component{
	function initialize(){
		$this->addComponent(new ActionLink($this, 'logout_do','Logout',$n=null));
	}
	function logout_do (){
		User::login('guest','guest');
		$this->triggerEvent('menuChanged', $n=null);
	}
}
?>
