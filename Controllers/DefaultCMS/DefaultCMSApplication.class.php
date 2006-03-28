<?php

class DefaultCMSApplication extends Application{
 	function &set_root_component() {
 		return new DefaultCMS;
 	}
}

?>