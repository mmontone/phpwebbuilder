<?php


class PostList extends CollectionNavigator {
  ...
  function addBackButton() {
    $this->addComponent(new CommandLink(
        array('text' => 'back',
              'proceedFunction' => new FunctionObject($this, 'callback')
        )
    ));
  }

  function showTag($params){
    $list = new PostList(Post::Tagged($params['tag']));
    $this->call($list);
    $list->addBackButton();
  }
  ...
}


?>