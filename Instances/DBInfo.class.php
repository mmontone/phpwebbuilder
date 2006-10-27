<?php

class DBInfo extends PersistentObject {

    function initialize() {
		$this->addField(new IndexField('version', array('type' => 'DBVersion')));
    }
}

?>