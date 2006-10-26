<?php

class LoginTest extends UnitTestCase {
	var $users;
	var $db;

	function LoginTest() {
		$this->UnitTestCase('Login test');
	}

	function setUp() {
		TestsDBSession:: setUp();
		$this->db =& DBSession::Instance();

		$this->db->SQLExec('CREATE TABLE `users` (
							`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
							`PWBversion` INT,
							`user` VARCHAR( 30 ) NOT NULL ,
							`pass` VARCHAR( 30 ) NOT NULL
							) TYPE = innodb;');
		$alex =& new User;
		$alex->user->setValue('alex');
		$alex->pass->setValue('alexpass');
		$alex->save();

		$marian =& new User;
		$marian->user->setValue('marian');
		$marian->pass->setValue('marianpass');
		$marian->save();

		$jero =& new User;
		$jero->user->setValue('jero');
		$jero->pass->setValue('jeropa');
		$jero->save();
	}

	function tearDown() {
		User::logout();
		$this->db->SQLExec('DROP TABLE `users`');
		TestsDBSession::release();
	}

	function testLogout() {
		User::login('marian', 'marianpass');
		User::logout();
		$user =& User::logged();
		$this->assertEqual($user->user->getValue(), 'guest', 'Checking logging out');
	}

	function testGuestLogin() {
		$user = & User :: logged();
		$this->assertEqual($user->user->getValue(), 'guest', 'Checking guest login');
	}

	function testSuccessLogin() {
		$user =& User::login('marian', 'marianpass');
		$this->assertNotEqual($user, false, 'Login in marian');
		$this->assertEqual($user->user->getValue(), 'marian', 'Logging in marian');
	}

	function testFailedLogin() {
		$this->assertFalse(User :: login('', ''), 'Checking annonymous login');
		$this->assertFalse(User :: login('admin', 'admin'), 'Checking login failure');
	}
}

?>