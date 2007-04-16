<?php

class Post extends PersistentObject {
  function initialize(){
    $this->addField(new TextField(array('fieldName'=>'titulo')));
    $this->addField(new TextArea(array('fieldName'=>'texto')));
    $this->addField(new CollectionField(
      array(
        'fieldName'=>'tags',
        'direct'=>false,
        'JoinType'=>PostTag,
        'targetField'=>'tag',
        'reverseField'=>'post')
       )
    );
    $this->addField(new IndexField(array('fieldName'=>'author', 'type'=>User)));
  }
}
?>