<?php

class AppConfig extends PersistentObject {

    function initialize() {
		$this->addIndexField('group',array('type' => 'ConfigGroup'));
    	$this->addTextField('app_class', array('display' => 'Application class'));
    	$this->addCollectionField('app', array('type' => 'ModuleConfig'));
    	$this->addTextField('rendering');
    	$this->addTextField('language');
    }
}

class ConfigGroup extends PersistentObject {
	function initialize() {
		$this->addIndexField('group',array('type' => 'ConfigGroup'));
    	$this->addTextField('app_class', array('display' => 'Application class'));
    	$this->addCollectionField('app', array('type' => 'ModuleConfig'));
    	$this->addTextField('rendering');
    	$this->addTextField('language');
    }
}

class ModuleConfig extends PersistentObject {
	function initialize() {
		$this->addIndexField('group',array('type' => 'ConfigGroup'));
    	$this->addTextField('app_class', array('display' => 'Application class'));
    	$this->addCollectionField('app', array('type' => 'ModuleConfig'));
    	$this->addTextField('rendering');
    	$this->addTextField('language');
    }
}
?>