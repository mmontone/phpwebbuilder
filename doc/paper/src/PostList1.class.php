<?php

class PostList extends CollectionNavigator {
  function addLine($post){
    return new PostItem($post);
  }
}

?>