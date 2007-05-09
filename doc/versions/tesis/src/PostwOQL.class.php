<?php

class Post extends Component {
  ...
  function Tagged($tag){
     return #@select p:Post
     			where exists
     				(p.tags as tag where tag.name=$tag)
     		@#;
  }
  ...
}


?>