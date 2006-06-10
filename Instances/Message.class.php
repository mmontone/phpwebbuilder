<?php

class Message extends PersistentObject {
   function initialize() {
   		$this->addTextField('message');
   }
}
?>