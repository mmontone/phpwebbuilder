<?php

require_once dirname(__FILE__) . '/HTMLObjectView.class.php';

class ObjectDisplayerView extends HTMLObjectView
{

  function ObjectDisplayerView(&$model, &$controller, &$config) {
    parent::HTMLObjectView($model, $controller, $config);
  }

    function renderTitle(&$html) {
    	$html->text('<h1>' . get_class($this->model) . '</h1>');
    }

    /* Fields rendering */

    function renderIdField(&$field, &$html) {}

    function renderCollectionField(&$field, &$html) {
        $collection_displayer =& $this->controller->componentAt($field->name);
        $collection_displayer->renderContent($html);
    }

    function renderTextField(&$field, &$html) {
        $view =& new TextFieldShowView($field, $this->config);
        $view->render_on($html);
    }
}
?>