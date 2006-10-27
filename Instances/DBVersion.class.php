<?php

class DBVersion extends PersistentObject {
	function initialize() {
		$this->addField(new NumField('version'));
		$this->addField(new TextArea('migration_code'));
	}
}

?>