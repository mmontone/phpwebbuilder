<?php

class AdminApplications extends DefaultCMSApplication{
 	function &setRootComponent() {
 		$comp =& new AdminMain;
 		return $comp;
 	}
}

?>