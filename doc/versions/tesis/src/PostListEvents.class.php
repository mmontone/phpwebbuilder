<?php

class PostList extends CollectionNavigator{
  function addLine($post){
    $pi = new PostItem($post);
    $pi->addInterestIn('tag_selected',
    	new FunctionObject($this, 'showTag'));
    return $pi;
  }
  ...
}

?>