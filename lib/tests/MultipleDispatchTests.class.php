<?php

class A {
	var $x;
}

class A1 extends A {}

class B {
	var $x;
}

class C {
	var $x;
}

class B1 extends B {}

class PadreDispatch {}
class PruebaDispatch extends PadreDispatch {}

function test1_PADREDISPATCH() {
	return true;
}

function test1_A_B(&$a, &$b) {
	return 'AB';
}


function test1_A_B1() {
	return 'AB1';
}

function test2_A_B1_C() {
	return 'ABC';
}


function mem_test_A_B(&$a, &$b) {
	$x = 2;
	$a->x =& $x;
}

class C1 extends Component {

}

class C2 extends Component {
	function initialize() {
		$this->addComponent(new C1, 'c1');
	}
}

class C3 extends Component {
	function initialize() {
		$this->addComponent(new C2, 'c2');
	}
}

class MultipleDispatchTests extends UnitTestCase {

    function MultipleDispatchTests() {
    	$this->UnitTestCase('Multiple dispatch tests');
    }

	function testMem() {
		$a =& new A;
		$x = 1;
		$a->x =& $x;

		$b =& new B;
		$y = 2;
		$b->x =& $y;

		mdcall('mem_test', array(&$a, &$b));
		$this->assertEqual($a->x, 2);
	}

	function test1() {
		$this->assertEqual(mdcall('test1', array(new A1, new B1)), 'AB1');
		$this->assertEqual(mdcall('test1', array(new A, new B)), 'AB');
		$this->assertEqual(mdcall('test1', array(new A1, new B)), 'AB');
		$this->assertFalse(mdcall('test1', array('hola', 'chau')));
		$this->assertTrue(mdcall('test1', array(new PruebaDispatch)));
	}

	function test2() {
		$this->assertEqual(mdcall('test2', array(new A1, new B1, new C)), 'ABC');
		$this->assertFalse(mdcall('test2', array(new A1, new B, new C)));
		$this->assertFalse(mdcall('test2', array(new A1, new B1)));
	}

	function test_md_get_layers() {
		$c3 =& new C3;
		$c3->initialize();
		$c2 =& $c3->c2;
		$c2->initialize();
		$c1 =& $c2->c1;
		$c1->initialize();
		$this->assertEqual(md_get_layers($c1), array('C3', 'C2', 'C1'));
		$this->assertEqual(md_get_layers($c2), array('C3', 'C2'));
		$this->assertEqual(md_get_layers($c3), array('C3'));
	}

	function test_mdcompcall() {
		$c3 =& new C3;
		$c3->initialize();
		$c2 =& $c3->c2;
		$c2->initialize();
		$c1 =& $c2->c1;
		$c1->initialize();

		$this->assertEqual(mdcompcall('testc1', array($c1)), 'c2c1');
	}


	function test_load_md_functions() {
		load_md_functions('/home/marian/workspace/pwb/lib/tests/md_tests.yml');
		$this->assertEqual(mdcall('test_func', array(new C1, new C2)), 'C1');

		$c3 =& new C3;
		$c3->initialize();
		$c2 =& $c3->c2;
		$c2->initialize();
		$c1 =& $c2->c1;
		$c1->initialize();
		$this->assertEqual(mdcompcall('test_comp_func', array($c1)), 'OK');
	}

}

function testc1_begctx_C1_endctx(&$c1) {
	return 'c1';
}

function testc1_begctx_C2_C1_endctx(&$c1) {
	return 'c2c1';
}

?>