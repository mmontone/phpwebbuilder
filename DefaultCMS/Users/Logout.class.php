<?

require_once pwbdir.'/Controllers/Controller.class.php';

class Logout extends Component{
	function initialize(){
		User::login('guest','guest');
		$this->triggerEvent('menuChanged', $n=null);
	}
}
?>
