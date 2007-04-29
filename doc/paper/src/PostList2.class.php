<?php


class PostList extends CollectionNavigator {
  function addLine($post){
    return new PostItem($post);
  }
  function showTag($params){
    $this->call(new PostList(Post::Tagged($params['tag'])));
  }
}


?>