<?php

class TranslationModuleConfig extends ModuleConfig {
	function initialize() {
		$this->addTextField('language',array('set' => array('Spanish', 'English')));
    }
}

?>