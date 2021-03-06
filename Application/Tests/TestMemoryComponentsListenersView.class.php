<?php

class TestMemoryComponentsListenersView extends UnitTestCase {
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
		$c0->addEventListener(array('test'=>'test'), $c1);
		$c1->addEventListener(array('test'=>'test'), $c0);
		$c1->delete();
		$this->assertEqual($m,strlen(session_encode()));
	}
	function testMemory_call_callback() {
		//The memory should be cleaned on a callback
		$c0 =& $_SESSION['comp'];
		$c0->addComponent($c1 = new Component);
		$m = strlen(session_encode());
		$c1->call($c2 = new Component);
		$c0->addEventListener(array('test'=>'test'), $c2);
		$c2->addEventListener(array('test'=>'test'), $c0);
		$c2->callback();
		$this->assertEqual($m,strlen(session_encode()));
	}
	function testMemory_StopAndCall() {
		//The memory should be cleaned on a stop and call
		$c0 =& $_SESSION['comp'];
		$c0->addComponent($c1 = new Component);
		$m = strlen(session_encode());
		$c0->addEventListener(array('test'=>'test'), $c1);
		$c1->addEventListener(array('test'=>'test'), $c0);
		$c1->stopAndCall($c2 = new Component);
		$this->assertEqual($m,strlen(session_encode()));
	}
	function testMemory_ChangeBody_Login() {
		//The memory should be cleaned on a stop and call
		$c0 =& $_SESSION['comp'];
		$c0->changeBody($c0->menu, $c1 = new Login);
		$m = strlen(session_encode());
		$c0->changeBody($c0->menu, $c2 = new Login);
		$this->assertEqual($m,strlen(session_encode()));
	}
	function testMemory_ChangeBody_Users() {
		//The memory should be cleaned on a stop and call
		$c0 =& $_SESSION['comp'];
		$c0->changeBody($c0->menu, $c1 = new CollectionViewer(new PersistentCollection(User)));
		$m = strlen(session_encode());
		$c0->changeBody($c0->menu, $c2 = new CollectionViewer(new PersistentCollection(User)));
		$this->assertEqual($m,strlen(session_encode()));
	}
}
?>