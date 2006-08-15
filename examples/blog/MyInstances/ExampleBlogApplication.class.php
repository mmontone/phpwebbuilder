<?php

class ExampleBlogApplication extends Application{

    function &setRootComponent(){
    	return new ExampleBlog();
    }
}
?>