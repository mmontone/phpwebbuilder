<?

class Logout extends Component{
	function initialize(){
		User::login('guest','guest');
		$this->triggerEvent('menuChanged', $n=null);
		$app =&Window::getActiveInstance();
		$app->navigate('Home',array());
	}
}
?>
