<?php

class ObjectDescription extends PersistentObject {
    function initialize() {
		$this->addTextField('class_name',array('isIndex'=>true, 'display'=>'Class name'));
		$this->addTextField('table');
		$this->addTextField('displayString');
		$this->addCollectionField('owner', array('type' => 'FieldDescription'));
    }
}
?>