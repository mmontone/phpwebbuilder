<?php

class Callback
{
    var $target;
    var $method_name;
    var $params;

    function Callback(&$target, $method_name, $params=array()) {
        $this->target =& $target;
        $this->method_name = $method_name;
        $this->params = $params;
    }

    function call($params) {
      $_params = array_merge((array)$params, $this->params);
        $method_name = $this->method_name;
        $this->target->$method_name($_params);
    }
}

function &callback(&$target, $callback, $params=array()) {
  return new Callback($target, $callback, $params);
}

?>