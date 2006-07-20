<?php
define('app_class','DefaultCMSApplication');
require_once '/var/www/eurekaweb/Configuration/pwbapp.php';
class TestComponents extends UnitTestCase {
	function setUp(){
		$app =& DefaultCMSApplication::instance();
		$_SESSION=array();
		$_SESSION['app'] =& $app;
	}
	/*function testMemoryAddComponentDeleteListener() {
		//The memory should be cleaned on a delete
		$c0 =& new Component;
		$_SESSION=array();
		$_SESSION['app'] =& $c0;
		$sc = $_SESSION;
		$this->dump($_SESSION);
		$m = strlen(session_encode());
		$c0->addComponent($c1 = new Component);
		$this->assertEqual(array(),$c0->event_listeners);
		$c0->addEventListener(array('test'=>'test'), $c1);
		$c1->delete();
		$this->assertEqual(array(),$c0->event_listeners);
		$this->assertEqual(array(),$c0->event_handles);
		$this->dump($_SESSION);
		$this->assertEqual($sc,$_SESSION);
		$this->assertEqual($m,strlen(session_encode()));
	}*/
	/*function testMemoryAddComponentDelete() {
		//The memory should be cleaned on a delete
		$c0 =& new Component;
		$_SESSION=array();
		$_SESSION['app'] =& $c0;
		$m = strlen(session_encode());
		$c0->addComponent($c1 = new Component);
		$c1->delete();
		$this->assertEqual($m,strlen(session_encode()));
	}
	function testMemoryView_AddComponentDelete() {
		//The memory should be cleaned on a delete
		$app =& $_SESSION['app'];
		$c0 =& $app->component;
		$m = strlen(session_encode());
		$c0->addComponent($c1 = new Component);
		$c1->delete();
		$this->assertEqual($m,strlen(session_encode()));
	}
	function testMemoryView_call_callback() {
		//The memory should be cleaned on a callback
		$app =& $_SESSION['app'];
		$c0 =& $app->component;
		$c0->addComponent($c1 = new Component);
		$m = strlen(session_encode());
		$c1->call($c2 = new Component);
		$c2->callback();
		$this->assertEqual($m,strlen(session_encode()));
	}
	function testMemoryView_StopAndCall() {
		//The memory should be cleaned on a stop and call
		$app =& $_SESSION['app'];
		$c0 =& $app->component;
		$c0->addComponent($c1 = new Component);
		$m = strlen(session_encode());
		$c1->stopAndCall($c2 = new Component);
		$this->assertEqual($m,strlen(session_encode()));
	}*/
	function testMemoryView_ChangeBody() {
		//The memory should be cleaned on a stop and call
		$app =& $_SESSION['app'];
		$c0 =& $app->component;
		$comp =& new Login;
		$c0->addComponent($comp, 'lala');
		$lm = strlen(session_encode());
 		//$comp->addEventListener(array('menuChanged'=>'updateMenu'),$c0);
 		//$comp->addEventListener(array('logged'=>'login'),$c0);
		//$c0->addComponent(new Login, 'lala');
		$comp->stopAndCall(new Login);
		$this->assertEqual($lm,strlen(session_encode()));
	}
	/*function testMemoryView_StopAndCallListener() {
		//The memory should be cleaned on a stop and call
		$app =& $_SESSION['app'];
		$c0 =& $app->component;
		$c0->addComponent($c1 = new Component);
		$m = strlen(session_encode());
		$c0->addEventListener(array('test'=>'test'), $c1);
		$c1->stopAndCall($c2 = new Component);
		$this->assertEqual($m,strlen(session_encode()));
	}*/
}
?>