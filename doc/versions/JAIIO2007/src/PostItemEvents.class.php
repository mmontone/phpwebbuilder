<?php

class PostItem extends Component{
  function initialize(){
    ...
    foreach($this->post->tags->collection->elements() as $tag){
      $this->tags->addComponent(new CommandLink(array(
        'text'=>$tag->nombre,
        'prodeedFunction'=>
        		new FunctionObject($this,
                                  'tagSelected',
                                  array('tag'=>$tag))
		)));
    }
  }
  function tagSelected($params){
    $this->triggerEvent('tag_selected', $params);
  }
}

?>