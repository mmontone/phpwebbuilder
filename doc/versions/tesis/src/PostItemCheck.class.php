<?php
class PostItem extends Component{
  function PostItem($post){
    #@typecheck $post: Post@#
    $this->post=&$post;
    parent::Component();
  }
  ...
}


?>