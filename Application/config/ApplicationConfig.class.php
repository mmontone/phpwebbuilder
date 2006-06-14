<?php

class ApplicationConfig extends PersistentObject {

    function initialize() {
		$this->addIndexField('group',array('type' => 'ConfigGroup'));
    	$this->addTextField('application_class', array('display' => 'Application class'));
    	$this->addTextField('base_dir', array('display' => 'Base dir'));
    	$this->addTextField('app_dir', array('display' => 'Application dir'));
    	$this->addTextField('pwb_url', array('display' => 'PWB URL'));
    	$this->addTextField('site_url', array('display' => 'Site URL'));
    	$this->addCollectionField('application', array('type' => 'ModuleConfig'));
    }
}

?>