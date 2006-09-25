<?php

class ColTestA extends PWBObject {}

class CollectionTests extends UnitTestCase {

    function CollectionTests() {
    	$this->UnitTestCase('Collection tests');
    }

    function testFirst() {
    	$c =& new Collection;
    	$f =& $c->first();
    	$this->assertEqual($f, null);
    	$this->assertTrue($c->size() == 0);

    	$e =& new ColTestA;
    	$c->add($e);
    	$this->assertTrue($e->is($c->first()));
    }

    function testRemove() {
		$c =& new Collection();
		$e =& new ColTestA;
		$this->assertEqual($c->remove($e), null);
		$c->add($e);
		$this->assertTrue($e->is($c->remove($e)));
		$this->assertEqual($c->size(), 0);
		$this->assertEqual($c->remove($e), null);
    }

    function testRemoveAll() {
		$c =& new Collection();
		$e =& new ColTestA;
		$c->add($e);
		$c->removeAll(array(&$e));
		$this->assertEqual($c->size(), 0);
    }

    function testAdd() {
    	$c =& new Collection;

    	for ($i = 0; $i < 10; $i++) {
    		$c->add($i);
    	}

    	for ($i = 0; $i < 10; $i++) {
    		$this->assertEqual($c->at($i), $i);
    	}

    	$this->assertEqual($c->size(), 10);
    }

    function testPushAndPop() {
		$c =& new Collection;

    	$this->assertNull($c->pop());

    	for ($i = 0; $i < 10; $i++) {
    		$c->push($i);
    	}

    	for ($i = 0; $i < 10; $i++) {
    		$elem =& $c->pop();
			$this->assertEqual($elem, $i);
    		/*
    		$this->assertEqual($c->size(), $i);

    		for ($j = $i - 1; $j >= 0; $j--) {
    			$this->assertEqual($c->at($j), $j + 1);
    		}*/
    	}
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
		$c->add($a = 1);
		$res = $c->foldr($v = 0,lambda('$acc, $x', 'return $acc + $x;', $b = array()));
		$this->assertEqual($res, 1);

		$c =& new Collection;
		$temp = array();
		for ($i = 1; $i <= 5; $i++) {
    		$temp[$i] = $i;
    		$c->add($temp[$i]);
    	}
		$res =& $c->foldr($v = 0,lambda('$acc, $x', 'return $acc + $x;', $a = array()));
		$this->assertEqual($res, 5 + 4 + 3 + 2 + 1);
    }

    function testMap() {
		$c =& new Collection;
		$c->map(lambda('&$x', 'return $x + 1;', $a = array()));
		$this->assertTrue($c->isEmpty());

		$temp = array();
		for ($i = 0; $i < 10; $i++) {
    		$temp[$i] = $i;
    		$c->add($temp[$i]);
    	}

		$c2 =& $c->map(lambda('$x', 'return $x + 1;', $a = array()));

		$temp = array();
		for ($i = 0; $i < 10; $i++) {
    		$temp[$i] = $i;
    		$this->assertEqual($c2->at($i), $temp[$i] + 1);
    	}
    }
}
?>