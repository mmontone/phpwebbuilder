<?php

class MessageTranslation extends PersistentObject {
    function initialize() {
    	$this->addIndexField('translator', array('type' => 'Translator'));
    	$this->addIndexField('message', array('type' => 'Message'));
    	$this->addTextField('translation');
    }
}
?>