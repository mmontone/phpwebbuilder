<?php

class OptionalSelectTest extends UnitTestCase {

    function OptionalSelectTest() {
    	$this->UnitTestCase('OptionalSelect widget tests');
    }

    function testInitialize() {
		$c =& new Collection;
		$v =& new ObjectHolder($o = null);
		$s =& new OptionalSelect($v, $c);
		$s->initialize();
		$this->assertTrue($s->nooption);

		$c =& new Collection;
		$o =& new PWBObject;
		$o->s = 'Hello';
		$c->add($o);
		$v =& new ObjectHolder($n = null);
		$s =& new Select($v, $c);
		$this->assertEqual($s->getValueIndex(), 0);

		$c =& new Collection;
		$o1 =& new PWBObject;
		$o1->s = 'Hello1';
		$o2 =& new PWBObject;
		$o2->s = 'Hello2';
		$c->add($o1);
		$c->add($o2);
		$v =& new ObjectHolder($o2);
		$s =& new Select($v, $c);
		$this->assertEqual($s->getValueIndex(), 1);
	}

	function testSetValueIndex() {
		$c =& new Collection;
		$o1 =& new PWBObject;
		$o1->s = 'Hello1';
		$o2 =& new PWBObject;
		$o2->s = 'Hello2';
		$c->add($o1);
		$c->add($o2);
		$v =& new ObjectHolder($o2);
		$s =& new Select($v, $c, lambda('&$c', 'return $c;', $a = array()));
		$s->setValueIndex($i = 0);
		$res =& $v->getValue();
		$this->assertEqual($res->s, 'Hello1');
		$s->setValueIndex($i = 1);
		$res =& $v->getValue();
		$this->assertEqual($res->s, 'Hello2');
	}
}
?>