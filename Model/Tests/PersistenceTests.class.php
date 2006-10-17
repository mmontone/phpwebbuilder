<?php

class PersistenceTests extends UnitTestCase {
	var $db;

    function PersistenceTests() {
    	$this->UnitTestCase('Persistence tests');
    }

    function setUp() {
		TestsDb::setUp();
		$this->db =& DB::Instance();
    }

    function testSave() {
		$alex =& new User;
		$alex->user->setValue('alex');
		$alex->pass->setValue('alexpass');
		$this->assertTrue($alex->save(), 'Saving a user');

		$alex=& User::getWithIndex('User',array('user' => 'alex'));

		$this->assertEqual($alex->user->getValue(), 'alex', 'Loading a user');
		$this->assertEqual($alex->pass->getValue(), 'alexpass', 'Loading a user');
	}
}

?>