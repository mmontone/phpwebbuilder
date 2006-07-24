<?

class Logout extends Component{
	function initialize(){
		User::login('guest','guest');
		$this->triggerEvent('menuChanged', $n=null);
		$app =&Application::instance();
		$app->navigate('Home',array());
	}
}
?>
