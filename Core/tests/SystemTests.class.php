<?php

class MyTopClass extends PWBObject {

}

class MyParentlessClass {

}

class SystemTests extends UnitTestCase {

    function SystemTests() {
    	$this->UnitTestCase('System tests');
    }

    function testIsMetaClass() {
		$this->assertTrue(PWBObject::IsMetaClass('PWBObject'));
		$this->assertTrue(PWBObject::IsMetaClass('DescriptedObject'));
		$this->assertFalse(PWBObject::IsMetaClass('MyTopClass'));
		$this->assertFalse(PWBObject::IsMetaClass('MyParentlessClass'));
    }

    function testIsTopClass() {
		$this->assertTrue(PWBObject::IsTopClass('PWBObject'));
		$this->assertTrue(PWBObject::IsTopClass('DescriptedObject'));
		$this->assertTrue(PWBObject::IsTopClass('MyTopClass'));
		$this->assertTrue(PWBObject::IsTopClass('MyParentlessClass'));
    }
}
?>