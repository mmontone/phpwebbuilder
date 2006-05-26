<?php

class MulticounterApplication extends Application
{
 	function &setRootComponent() {
 		return new MultiCounter;
 	}
}

?>