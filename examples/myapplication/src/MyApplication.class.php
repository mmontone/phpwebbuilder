<?php

class MyApplication extends Application
{
 	function &set_root_component() {
 		return new MyComponent;
 	}
}

?>