<?php

class Module extends DescriptedObject {
    function initialize() {
    	$this->addTextField('directory');
    	$this->addCollectionField('dependent', array('type' => 'ModuleDependency'));
    }
}

class ModuleDependency extends DescriptedObject {
	function initialize() {
		$this->addIndexField('dependent', array('type' => 'Module'));
		$this->addIndexField('module', array('type' => 'Module'));
	}
}
?>