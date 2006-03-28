<?php

require_once dirname(__FILE__) . '/Loader.class.php';

class PersistentCollectionLoader extends Loader  {
    function readForm ($form, &$error_msgs) {
         foreach ($this->obj->objects() as $index=>$object) {
         	$html =& $this->loadFor($object);
            $html->readForm($form, &$error_msgs);
         }
         if (isset($form["conditions"])) $this->obj->conditions=unserialize(stripslashes($form["conditions"]));
         trace("conditions:".print_r(unserialize(stripslashes($form["conditions"]))), TRUE);
         if (isset($form["order"]))$this->obj->order=$form["order"];
         if (isset($form["offset"]))$this->obj->offset=$form["offset"];
         if (isset($form["slimit"]))$this->obj->limit=$form["limit"];
         return TRUE;
    }
}

?>
