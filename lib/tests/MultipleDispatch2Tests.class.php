<?php

require_once pwbdir . '/lib/md2.php';

class A {}

class B extends A {}

class C1 extends Component {}

class C2 extends C1 {}

class C3 extends C2 {}

class MultipleDispatch2Tests extends UnitTestCase {
    function MultipleDispatch2Tests() {
    	$this->UnitTestCase('Multiple dispatch v2 tests');
    }

	function testIsA() {
		$this->assertTrue(str_is_a('B', 'A'));
		$this->assertFalse(str_is_a('A', 'B'));
	}

	function testCompiler() {
		$c =& new OrdinaryMDCompiler();
		$c->loadMDFunctions(pwbdir .'/lib/tests/md2_tests.md');
		$c->LoadMap();
		$this->assertEqual($c->call('test_func', array(new C1, new C2)), 'C1C2');
		$this->assertEqual($c->call('test_func', array(new C1, new C1)), 'C1C1');
	}

	function testContextCall() {

		$c =& new ContextMDCompiler();
		$c->loadMDFunctions(pwbdir .'/lib/tests/md2_tests.md');
		$c->LoadMap();

		$this->assertEqual($c->call('test_func', new C1, array(new C1, new C2)), 'C1');
		$this->assertEqual($c->call('test_func', new C2, array(new C1, new C1)), 'C2');

		$c1 =& new C1;
		$c2 =& new C2;
		$c2->addComponent($c1, 'c1');
		$c2->initialize();
		$this->assertEqual($c->call('test_func', $c1, array(new C1, new C1)), 'C1');

		$c1 =& new C1;
		$c2 =& new C2;
		$c3 =& new C3;
		$c2->addComponent($c1, 'c1');
		$c2->initialize();
		$c3->addComponent($c2, 'c2');
		$c3->initialize();
		$this->assertEqual($c->call('test_func', $c1, array(new C1, new C1)), 'C1');

		/*------------------------*/
		$c1 =& new C1;
		$c2 =& new C2;
		$c2->addComponent($c1, 'c1');
		$c2->initialize();
		$this->assertEqual($c->call('test_func2', $c1, array(new C1, new C1)), 'C1C1C1');

		$c1 =& new C1;
		$c2 =& new C1;
		$c3 =& new C3;
		$c2->addComponent($c1, 'c1');
		$c2->initialize();
		$c3->addComponent($c2, 'c2');
		$c3->initialize();
		$this->assertEqual($c->call('test_func2', $c1, array(new C1, new C1)), 'C1C1C1');

		$c1 =& new C1;
		$c2 =& new C2;
		$c3 =& new C1;
		$c2->addComponent($c1, 'c1');
		$c2->initialize();
		$c3->addComponent($c2, 'c2');
		$c3->initialize();
		$this->assertEqual($c->call('test_func2', $c1, array(new C1, new C1)), 'C1C2C1');

	}

	function testContextIsMoreSpecific() {

	}
}


?>