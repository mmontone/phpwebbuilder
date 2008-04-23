<?php

class AdminApplicationsApplication extends DefaultCMSApplication{
 	function &setRootComponent() {
 		$comp =& new AdminMain;
 		return $comp;
 	}
}

?>