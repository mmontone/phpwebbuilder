<?php

class Callback
{
    var $target;
    var $method_name;
    var $params;

    function Callback(&$target, $method_name, $params) {
        $this->target =& $target;
        $this->method_name = $method_name;
        $this->params = $params;
    }

    function call($params) {
    	$_params = array_merge($params, $this->params);
        $method_name = $this->method_name;
        $this->target->$method_name($_params);
    }
}

?>