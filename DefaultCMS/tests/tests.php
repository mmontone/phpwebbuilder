<?php

require_once 'LoginTest.class.php';

class UsersAndPermissionsTests extends GroupTest {
	function UsersAndPermissionsTests() {
		$this->GroupTest('Users and permissions tests');
		$this->addTestCase(new LoginTest);
	}
}
?>
