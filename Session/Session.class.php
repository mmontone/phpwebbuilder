<?php

class Session extends PersistentObject{
	function initialize(){
		$this->addField(new TextField('session_name', TRUE));
		$this->addField(new TextField('session_id', TRUE));
		$this->addField(new DateTimeField('date_created', TRUE));
		$this->addField(new DateTimeField('last_updated', FALSE));
		$this->addField(new BlobField('session_data', FALSE));
	}
}

?>