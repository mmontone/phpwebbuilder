<?php

class PersistenceTests extends UnitTestCase {
	var $db;

    function PersistenceTests() {
    	$this->UnitTestCase('Persistence tests');
    }

    function setUp() {
		TestsDb::setUp();
		$this->db =& DB::Instance();

		$this->db->SQLExec('CREATE TABLE `users` (
							`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
							`PWBversion` INT,
							`user` VARCHAR( 30 ) NOT NULL ,
							`pass` VARCHAR( 30 ) NOT NULL
							) TYPE = innodb;');

    }

    function testSave() {
		$alex =& new User;
		$alex->user->setValue('alex');
		$alex->pass->setValue('alexpass');
		$this->assertTrue($alex->save(), 'Saving a user');

		$alex=& User::getById('User',0);
		$this->assertEqual($alex->user->getValue(), 'alex', 'Loading a user');
	}
}

?>