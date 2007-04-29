<?php

class BlogComponent extends Component {
  function initialize(){
      $this->addComponent(new PostList(#@select Post@#));
  }
}

?>