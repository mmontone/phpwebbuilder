<?php

class Tag extends PersistentObject {
  function initialize(){
    $this->addField(new TextField(array('fieldName'=>'nombre', 'is_index'=>true)));
    $this->addField(new CollectionField(
      array(
        'fieldName'=>'posts',
        'direct'=>false,
        'JoinType'=>'PostTag',
        'targetField'=>'post',
        'reverseField'=>'tag')
       )
    );
  }
}

class PostTag extends PersistentObject {
  function initialize(){
    $this->addField(new IndexField(array('fieldName'=>'post', 'type'=>Post, 'is_index'=>true)));
    $this->addField(new IndexField(array('fieldName'=>'tag', 'type'=>Tag, 'is_index'=>true)));
  }
}

?>