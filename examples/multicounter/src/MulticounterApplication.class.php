<?php

class MulticounterApplication extends Application
{
 	function &set_root_component() {
 		return new MultiCounter;
 	}
}

?>