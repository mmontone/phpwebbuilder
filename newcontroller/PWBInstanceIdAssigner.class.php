<?php

$id_assigner_instance = null;

class PWBInstanceIdAssigner {
	var $__id = 1;

    function &instance() {
    	global $id_assigner_instance;
        if ($id_assigner_instance == null) {
            $id_assigner_instance = new PWBInstanceIdAssigner();
        }
        return $id_assigner_instance;
    }

    function assignIdTo(&$pwb_object) {
    	$pwb_object->__instance_id = $this->__instance_id ++;
    }
}

?>