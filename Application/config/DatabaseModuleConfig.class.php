<?php

class DatabaseModuleConfig extends ModuleConfig {
	function initialize() {
		$this->addTextField('driver',array('set' => array('MySQL')));
		$this->addTextField('tables_prefix', array('display' => 'Tables prefix'));
    }
}

?>