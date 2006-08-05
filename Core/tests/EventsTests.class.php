<?php

class EventsTests extends UnitTestCase {

    function EventsTests() {
    	$this->UnitTestCase('Events tests');
    }

    function testChanged() {
		$o =& new PWBObject();
		$o->addEventListener(array('changed' => 'objectChanged'), $this);
    }

}
?>