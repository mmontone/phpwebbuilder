<?php
 class ExampleBlog extends Component {
    function initialize(){
      $this->addPostForm();
   /*Needs database access*/
      /*$blogposts =& new PersistentCollection('Post');
      $blogposts->map(
         lambda('&$p',
            '$this->addPost($p);',
            get_defined_vars()
         )
      );*/
   }
   /*Needs database access*/
   /*function checkProcessPostFormPermissions(){
   	   $u =& User::logged();
   	   return $u->hasPermission('Poster');
   }*/
   function addPost(&$post){
       $title =& new Label($post->title->getValue());
       $this->addComponent($title);
       $this->addComponent(new Label($post->body->getValue()));
   }
   function addPostForm(){
      $cf =& new Component();
      $this->addComponent($cf, 'addPost');
      $cf->addComponent(new Input(new ValueHolder($title="")), 'title');
      $cf->addComponent(new TextAreaComponent(new ValueHolder($title="")), 'body');
      $cf->addComponent(new ActionLink($this, 'processPostForm', 'Submit', $n=null), 'post');
   }
   function processPostForm(){
      $p =& new Post;
      $p->title->setValue($this->addPost->title->getValue());
      $p->body->setValue($this->addPost->body->getValue());
      /*Needs database access*/
      //$p->save();
      $this->addPost($p);
      $this->addPostForm();
   }

 }



?>