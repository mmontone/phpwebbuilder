<?php

require_once dirname(__FILE__)."/../CollectionView.class.php";

class HTMLCollectionView extends CollectionView
{
    var $collection;
    var $layout;
    var $link_view;
    var $config;

    function renderTitle(&$html) {
        $html->text('<h1>' . $this->model->datatype . 's </h1>');
    }

    function beginCollectionRendering(&$html) {
        $this->triggerEvent('about_to_begin_collection_rendering', array('view' => $this,
                                                                         'html' => $html));
        $this->layout->beginCollectionRendering($html);
    }

    function endCollectionRendering(&$html) {
        $this->layout->endCollectionRendering($html);
        $this->triggerEvent('collection_rendered', array('view' => $this,
                                                         'html' => $html));
        $this->renderActions($html);
    }

    function beginElementRendering(&$element, &$html) {
        $this->triggerEvent('about_to_begin_element_rendering', array('view' => $this,
                                                                    'html' => $html,
                                                                    'element' => $element));
        $this->layout->beginElementRendering(&$html);
    }

    function endElementRendering(&$element, &$html) {
        $this->layout->endElementRendering(&$html);
        $this->triggerEvent('field_rendered', array('view' => $this,
                                                    'html' => $html,
                                                    'element' => $element));
        $this->renderElementActions($element, $html);
    }

    /* Actions rendering */

    function renderActions(&$html) {
    	$this->controller->renderActions($html);
    }

    function renderElementActions(&$element, &$html) {
      $this->controller->renderElementActions($element, $html);
    }

    function renderEditAction($link, &$html) {
    	$this->link_view->renderEditLink($link, $html);
    }

    function renderSaveAction($link, &$html) {
      $this->link_view->renderSaveLink($link, $html);
    }

    function renderCancelAction($link, &$html) {
      $html->text("<a href=$link>Cancel</a>");
    }
}

?>