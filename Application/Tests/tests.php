<?php
require_once dirname(__FILE__).'/../../Core/Core.php';
require_once dirname(__FILE__).'/../Application.php';
session_start();
class test2 extends UnitTestCase {

	function testMem() {
		$c0 =& new Component;
		$_SESSION['lala'] =& $c0;
		$m = strlen(session_encode());
		$c0->addComponent($c1 = new Component);
		$c1->delete();
		$this->assertEqual($m,strlen(session_encode()));
	}
}
?>