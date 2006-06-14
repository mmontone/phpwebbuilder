<?php

class ViewModuleConfig extends ModuleConfig {
	function initialize() {
		$this->addTextField('rendering',array('set' => array('Standard', 'AJAX')));
    }
}

?>