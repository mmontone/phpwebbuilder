<?php

$id_assigner_instance = null;

class PWBInstanceIdAssigner {
    function assignIdTo(&$pwb_object) {
    	static $__instance_id = 1;
    	$pwb_object->__instance_id = $__instance_id++;
    }
}

?>