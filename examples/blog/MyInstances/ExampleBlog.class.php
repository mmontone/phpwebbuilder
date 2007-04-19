<?php
 class ExampleBlog extends Component {
    function initialize(){
      //print_r(DBSession::Instance()->batchExec(array("SELECT tbl_name FROM sqlite_master")));
      //DBSession::Instance()->batchExec(explode(';',TablesChecker::checkTables(false)));
      $this->addComponent(new AddPost(), 'addPost');
      $blogposts =& new PersistentCollection('Post');
      $self =& $this;
      $blogposts->map(
         lambda('&$p',
            '$self->addPost($p);',
            get_defined_vars()
         )
      );
   }
   function addPost(&$post){
       $title =& new Label($post->title->getValue());
       $this->addComponent($title);
       $this->addComponent(new Label($post->body->getValue()));
   }
 }

class AddPost extends Component{
	function initialize(){
	  $this->post =& new Post;
      $this->addComponent(new Input($this->post->title), 'title');
      $this->addComponent(new TextAreaComponent($this->post->body), 'body');
      $this->addComponent(new ActionLink($this, 'processPostForm', 'Submit', $n=null), 'post');
	}
   function processPostForm(){
      $this->post->save();
      $this->getParent()->addPost($this->post);
      $this->stopAndCall(new AddPost);
   }
}

?>