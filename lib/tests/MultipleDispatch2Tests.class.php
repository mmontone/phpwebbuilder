<?php

require_once pwbdir . '/lib/md2.php';

class A {}

class B extends A {}

class C1 extends Component {}

class C2 extends C1 {}

class C3 extends C2 {}

#@defmdfsignature test_func ()@#

#@defmdf test_func [C2] ()
{
	return 'C2';
}//@#

#@defmdf test_func [C1] ()
{
	return 'C1';
}//@#

#@defmdfsignature test_func11 (&$c1 : C1, &$c2 : C2)@#

#@defmdf test_func11 (&$c1 : C1, &$c2 : C2)
{
    return 'C1C2';
}//@#

#@defmdf test_func11 (&$c1 : C1, &$c2 : C1)
{
    return 'C1C1';
}//@#

#@defmdfsignature test_func2 ()#@
#@defmdf test_func2 [C1, C1] ()
{
    return 'C1C1';
}//@#

#@defmdf test_func2 [C2, C1] ()
{
  return 'C2C1';
}//@#

#@defmdf test_func2 [C2] ()
{
  return 'C2';
}//@#

#@defmdf test_func2 [C1] ()
{
  return 'C1';
}//@#

#@defmdf test_func2 [C1, C1, C1] ()
{
  return 'C1C1C1';
}//@#

#@defmdf test_func2 [C1, C2, C1] ()
{
  return 'C1C2C1';
}//@#

#@defmdfsignature test_func22 (&$c1 : C1, &$c2 : C2)@#

#@defmdf test_func22 (&$c1 : C1, &$c2 : C2)
{
    return 'C1C2';
}//@#


#@defmdf test_func22 (&$c1 : C1, &$c2 : C1)
{
    return 'C1C1';
}//@#

#@defmdfsignature test_comp_func1 ()@#
#@defmdfsignature test_comp_func2 (&$c1 : C1, &$c2 : C2)@#

#@defmdf test_comp_func1 ()
{
    return 'OK';
}//@#

#@defmdf test_comp_func2 ()
{
    return 'OK';
}//@#


class MultipleDispatch2Tests extends UnitTestCase {
    function MultipleDispatch2Tests() {
    	$this->UnitTestCase('Multiple dispatch v2 tests');
    }

	function testIsA() {
		$this->assertTrue(str_is_a('B', 'A'));
		$this->assertFalse(str_is_a('A', 'B'));
	}

	function testCompiler() {
		$c =& new MDCompiler();
		$this->assertEqual($c->call('test_func', array(new C1, new C2)), 'C1C2');
		$this->assertEqual($c->call('test_func', array(new C1, new C1)), 'C1C1');
	}

	/*
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

	}*/
}


?>