<?php

class DBVersion extends PersistentObject {
	function initialize() {
		$this->addField(new NumField('version', array('is_index' => true)));
		$this->addField(new TextArea('migration_code'));
		$this->addField(new TextArea('sql'));
	}
}

?>