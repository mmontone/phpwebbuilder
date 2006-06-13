<?php

class ObjectDescription extends PersistentObject {
    function initialize() {
		$this->table = 'object_descriptions';
		$this->addTextField('class_name',array('isIndex'=>true, 'display'=>'Class name'));
		$this->addTextField('table_name');
		$this->addTextField('display_string');
		$this->addCollectionField('owner', array('type' => 'FieldDescription'));
    }
}
?>