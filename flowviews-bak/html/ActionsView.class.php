<?php

class ActionsView
{
    var $actions;
    var $layout;

    function ActionsView($actions, &$layout) {
        $this->actions = $actions;
        $this->layout =& $layout;
    }

    function render(&$action_renderer, &$html) {
        $this->beginActionsRendering(&$html);
        foreach ($this->actions as $action) {
            $this->layout->beginActionRendering($html);
            $func_name = 'render' . $action . 'Link';
        	$action_renderer->$func_name(&$html);
            $this->layout->endActionRendering($html);
        }
        $this->endActionsRendering($html);
    }
}

?>