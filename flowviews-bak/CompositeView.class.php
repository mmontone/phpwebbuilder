<?php

class CompositeView extends Renderer{

    function CompositeView($params) {
        $model =& $params['model'];
        $this->obj =& $model;
        $linker =& new $params['linker']($params['controller']);
        $structure =& new $params['structure']($model, $linker);
        $action =& new $params['action'];
        parent::Renderer($structure, $action, $linker);
    }
    function renderOn(&$out) {
        $out = $this->render($this->obj);
    }
}
?>