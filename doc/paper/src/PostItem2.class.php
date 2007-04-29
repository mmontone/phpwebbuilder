<?php

class PostItem extends Component {
  function PostItem($post){
    $this->post = $post;
    parent::Component();
  }
  function initialize(){
    $this->addComponent(new Text($this->post->titulo), 'title');
    $this->addComponent(new Text($this->post->texto), 'text');
    // Mostramos las etiquetas del post
    $this->addComponent(new Component, 'tags');
    foreach($this->post->tags->collection->elements() as $tag){
      $this->tags->addComponent(new CommandLink(array(
        'text'=>$tag->name,
        'prodeedFunction'=>
          new FunctionObject(
            $this->getParent(),
            'showTag',
            array('tag'=>$tag)
          )
        ))
      );
    }
  }
}

?>