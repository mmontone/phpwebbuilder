<?php

class ServiceRequest
{
    var $id;
    var $params;
    var $firing_object;

    function ServiceRequest($id, $firing_object, $params) {
        $this->id = $id;
        $this->firing_object = $firing_object;
        $this->params = $params;
    }
}

?>