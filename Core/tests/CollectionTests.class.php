<?php

class CollectionTests extends UnitTestCase {

    function CollectionTests() {
    	$this->UnitTestCase('Collection tests');
    }

    function testAdd() {
    	$c =& new Collection;

    	for ($i = 0; $i < 10; $i++) {
    		$c->add($i);
    	}

    	for ($i = 0; $i < 10; $i++) {
    		$this->assertEqual($c->at($i), $i);
    	}

    	$this->assertTrue($c->size() == 10);
    }

    function testIsEmpty() {
		$c =& new Collection;
    	$this->assertTrue($c->isEmpty());
    	$c->add($v = 1);
    	$this->assertFalse($c->isEmpty());
    }

    function testFoldr() {
		$c =& new Collection;
		$res =& $c->foldr($v = 0,lambda('&$acc, &$x', 'return $acc + $x;', $a = array()));
		$this->assertTrue($c->isEmpty());
		$this->assertEqual($res, 0);

		$c =& new Collection;
		$c->add($v = 1);
		$res = $c->foldr($v = 0,lambda('$acc, $x', 'return $acc + $x;', $a = array()));
		$this->assertEqual($res, 1);

		$c =& new Collection;
		for ($i = 1; $i <= 5; $i++) {
    		$c->add($i);
    	}
		$res =& $c->foldr($v = 0,lambda('$acc, $x', 'return $acc + $x;', $a = array()));
		$this->assertEqual($res, 5 + 4 + 3 + 2 + 1);
    }

    function testMap() {
		$c =& new Collection;
		$c->map(lambda('&$x', 'return $x + 1;', $a = array()));
		$this->assertTrue($c->isEmpty());

		for ($i = 0; $i < 10; $i++) {
    		$c->add($i);
    	}

		$c2 =& $c->map(lambda('$x', 'return $x + 1;', $a = array()));

		for ($i = 0; $i < 10; $i++) {
    		$this->assertEqual($c2->at($i), $i + 1);
    	}
    }
}
?>