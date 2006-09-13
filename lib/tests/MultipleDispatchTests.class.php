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

class PruebaDispatch {}

function test1_PRUEBADISPATCH() {
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
}

?>