<?php
define('app_class','DefaultCMSApplication');
require_once '/var/www/eurekaweb/Configuration/pwbapp.php';
class TestMemoryChangeBody extends UnitTestCase {
	function setUp(){
		$c0 =& new Component;
		$_SESSION=array();
		$_SESSION['app']  =& Application::instance();
		$_SESSION['comp'] =& $_SESSION['app']->component;
	}
	function tearDown(){
		$_SESSION=array();
	}
	function testMemory_ChangeBody_Login() {
		//The memory should be cleaned on a stop and call
		$c0 =& $_SESSION['comp'];

		$c1 =& new Login;
		$c0->addComponent($c1, 'body');
 		$c1->addEventListener(array('menuChanged'=>'updateMenu'),$c0);
 		$c1->addEventListener(array('logged'=>'login'),$c0);

		$m = session_encode();
		$lm =strlen($m);

		$c1 =& new Login;
		$c0->addComponent($c1, 'body');
 		$c1->addEventListener(array('menuChanged'=>'updateMenu'),$c0);
 		$c1->addEventListener(array('logged'=>'login'),$c0);
 		$ind=0;
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
	/*function testMemory_ChangeBody_Users() {
		//The memory should be cleaned on a stop and call
		$c0 =& $_SESSION['comp'];
		$c0->changeBody($c0->menu, $c1 = new CollectionViewer(new PersistentCollection(User)));
		$m = strlen(session_encode());
		$c0->changeBody($c0->menu, $c2 = new CollectionViewer(new PersistentCollection(User)));
		$this->assertEqual($m,strlen(session_encode()));
	}*/
}
?>