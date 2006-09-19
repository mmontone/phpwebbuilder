<?php

require_once dirname(__FILE__) . '/../qsort.php';

function lower($a, $b) {
	return $a < $b;
}

class QSortTests extends UnitTestCase {

    function QSortTests() {
    	$this->UnitTestCase('Quick-Sort tests');
    }

    function testEmpty() {
    	$array = array();
    	qsort($array, 'lower');
    	$this->assertTrue(empty($array));
    }

    function testSingle() {
    	$array = array(1);
    	qsort($array, 'lower');
    	$this->assertEqual($array, array(1));
    }

    function testDuplicates() {
    	$array = array(3,2,1,2,1);
    	qsort($array, 'lower');
    	$this->assertEqual($array, array(1,1,2,2,3));
    }

    function testReferences() {

    }

    function testOrder() {
		$array = array(4,3,2,1);
    	qsort($array, 'lower');
    	$this->assertEqual($array, array(1,2,3,4));

    	$array = array(6,9,1,10);
    	qsort($array,'lower');
    	$this->assertEqual($array, array(1,6,9,10));
    }
}

?>