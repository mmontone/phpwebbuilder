<?php

class TestMemoryComponents extends UnitTestCase {
	function setUp(){
		$c0 =& new Component;
		$_SESSION=array();
		$_SESSION['comp'] =& $c0;
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
		$m = session_encode();
		$c1->call($c2 = new Component);
		$c2->callback();
		$lm = strlen($m);
 		$regs = array('i:[0-9]*;',
 				'\"event_handles\";a:[0-9]*');
 		$v2 = session_encode(); $v1 = $m;
 		foreach($regs as $reg){
	 		$v1 = ereg_replace($reg,$reg,$v1);
	 		$v2 = ereg_replace($reg,$reg,$v2);
 		}
		$this->assertEqual($v1,$v2);
		$this->assertEqual($lm,strlen(session_encode()));
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