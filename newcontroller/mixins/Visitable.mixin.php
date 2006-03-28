<?php

class Visitable
{
   function visit(&$obj) {
        $method_name = 'visited' . $this->get_class();
        $obj->$method_name($this);
    }
}

?>