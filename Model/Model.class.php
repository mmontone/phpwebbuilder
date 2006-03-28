<?php

class Model
{
    var $controller;
    var $view;

    function renderAction($action) {
        $this->controller->renderAction($action);
    }
}
?>