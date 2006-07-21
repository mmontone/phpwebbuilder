<?php
define('app_class','DefaultCMSApplication');
require_once '/var/www/eurekaweb/Configuration/pwbapp.php';
class TestMemoryComponentsView extends UnitTestCase {
	function setUp(){
		$c0 =& new Component;
		$_SESSION=array();
		$_SESSION['app']  =& Application::instance();
		$_SESSION['comp'] =& $_SESSION['app']->component;
	}
	function tearDown(){
		$_SESSION=array();
	}
	function testMemoryAddComponentDelete() {
		//The memory should be cleaned on a delete
		$c0 =& $_SESSION['comp'];
		$m = strlen(session_encode());
		$c0->addComponent($c1 = new Component);
		$c1->delete();
		$this->assertEqual($m,strlen(session_encode()));
	}
	function testMemory_call_callback() {
		//The memory should be cleaned on a callback
		$c0 =& $_SESSION['comp'];
		$c0->addComponent($c1 = new Component);
		$m = strlen(session_encode());
		$c1->call($c2 = new Component);
		$c2->callback();
		$this->assertEqual($m,strlen(session_encode()));
	}
	function testMemory_StopAndCall() {
		//The memory should be cleaned on a stop and call
		$c0 =& $_SESSION['comp'];
		$c0->addComponent($c1 = new Component);
		$m = strlen(session_encode());
		$c1->stopAndCall($c2 = new Component);
		$this->assertEqual($m,strlen(session_encode()));
	}
}
?>