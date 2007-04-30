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
        $this->view->setAttribute('onClick', 'window.open(\'' . toAjax($this->component->target)  .'\', \'Visor de imágen\', \'popup\');');
    }
    function initializeDefaultView(&$view){
        $view->setTagName('img');
    }
    function setEvents(&$comp){}
}

?>