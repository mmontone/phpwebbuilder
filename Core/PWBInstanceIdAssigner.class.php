<?php

class PWBInstanceIdAssigner {
    function assignIdTo(&$pwb_object) {
    	if (!isset($_SESSION[sitename]['instance_id'])) $_SESSION[sitename]['instance_id'] = 1;
    	$pwb_object->__instance_id = $_SESSION[sitename]['instance_id']++;
    }
}

?>