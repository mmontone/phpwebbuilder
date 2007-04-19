<?php

class ImageDisplayHTMLHandler extends WidgetHTMLHandler{
    function & createDefaultView() {
         $v = & new XMLNodeModificationsTracker;
        $v->appendChild(new HTMLContainer('',array('id'=>'linkName')));
        $this->initializeDefaultView($v);
        return $v;
    }
    function prepareToRender(){
        parent::prepareToRender();
         $this->view->setAttribute('src', toAjax($this->component->target));
        $this->view->setAttribute('alt', toAjax($this->component->alt));
        if ($this->component->targetFrame){
            $this->view->setAttribute('target', toAjax($this->component->targetFrame));
        }
        $this->view->addCSSClass('clickable');
    }
    function initializeDefaultView(&$view){
        $view->setTagName('img');
    }
    function setEvents(&$comp){}
}

?>