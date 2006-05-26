<?php

require_once 'UITests.class.php';

class UITestsApplication extends Application {
    function &setRootComponent() {
 		return new UITests;
 	}
}

?>