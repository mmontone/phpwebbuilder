<?php

class DownloadFileLink extends Link {
    var $image;
    var $alt;
    function DownloadFileLink(&$img, $alt='Imagen'){
        parent::Link($img->downloadLink(), '');
        $this->alt = $alt;
    }

    function initializeDefaultView(&$view){
        $view->setTagName('a');
    }

    function prepareToRender(){
        parent::prepareToRender();
        $this->view->setAttribute('src', toAjax($this->target));
        $this->view->setAttribute('alt', toAjax($this->alt));
    }
}

class ImageDisplay extends Link {
    var $image;
    var $alt;
    function ImageDisplay(&$img, $alt='Imagen'){
        parent::Link($img->displayLink(), '');
        $this->alt = $alt;
     }
}

?>