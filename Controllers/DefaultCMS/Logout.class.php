<?
class Logout extends Controller{
	function begin(){
	    session_destroy();
	    header("location:Action.php");
	}
}
?>
