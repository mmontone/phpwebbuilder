<?php

class ModuleConfig extends PersistentObject {
	function initialize() {
		$this->addIndexField('application',array('type' => 'ApplicationConfig'));
		$this->addIndexField('module', array('type' => 'Module'));
    }
}

?>