<?php

class ToolbarButtonHandler extends WidgetXULHandler {
    function setView(&$view){
        parent::setView($view);

        $view->setAttribute('label', $this->component->textv->getValue());
    }
}
?>