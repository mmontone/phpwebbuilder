<?php

class PostItem extends Component {
  function PostItem($post){
    $this->post = $post;
    parent::Component();
  }
  function initialize(){
    $this->addComponent(new Text($this->post->title), 'title');
    $this->addComponent(new Text($this->post->text), 'text');
    // Mostramos las etiquetas del post
    $this->addComponent(new Component, 'tags');
    foreach($this->post->tags->collection->elements() as $tag){
      $this->tags->addComponent(new Text($tag->name));
    }
  }
}

?>