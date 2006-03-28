<?php

require_once dirname(__FILE__) . '/HTMLObjectView.class.php';

class ObjectEditorView extends HTMLObjectView
{

  function ObjectEditorView(&$model, &$controller, &$config) {
    parent::HTMLObjectView($model, $controller, $config);
  }

    function renderTitle(&$html) {
    	$html->text('<h1>' . get_class($this->model) . ' edition</h1>');
    }

    /* Fields rendering */

    function renderIdField(&$field, &$html) {}

    function renderCollectionField(&$field, &$html) {
        $collection_displayer =& $this->controller->component_at($field->name);
        $collection_displayer->renderContent($html);
    }

    function renderTextField(&$field, &$html) {
        $view =& new TextFieldEditView($field, $this->config);
        $view->render_on($html);
    }
}

?>