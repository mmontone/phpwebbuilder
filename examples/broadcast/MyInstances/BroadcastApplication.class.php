<?php

class BroadcastApplication extends Application
{
 	function &setRootComponent() {
 		return new MultiCounter;
 	}
}

?>