<?php

class ConfigGroup extends PersistentObject {
	function initialize() {
		$this->addTextField('group_name', array('display' => 'Group name', 'isIndex' => true));
		$this->addIndexField('application');
    }
}
?>