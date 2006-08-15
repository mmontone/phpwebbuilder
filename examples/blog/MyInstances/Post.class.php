<?php

class Post extends PersistentObject{
   function initialize() {
      $this->addField(new TextField(array('fieldName'=>'title', 'is_index'=>TRUE)));
      $this->addField(new TextArea(array('fieldName'=>'body', 'is_index'=>FALSE)));
      $this->addField(new DateTimeField(array('fieldName'=>'created_date', 'is_index'=>FALSE)));
      $this->addField(new DateTimeField(array('fieldName'=>'modified_date', 'is_index'=>FALSE)));
   }
 }
?>