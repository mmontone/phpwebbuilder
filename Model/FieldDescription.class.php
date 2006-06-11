<?php

class FieldDescription extends PersistentObject {
    function initialize() {
    	$this->addIndexField('owner', array('type'=>'ObjectDescription'));
    	$this->addTextField('name', array('isIndex'=>true));
    	$this->addBoolField('isIndex');
    	$this->addTextField('display');
    }
}
?>