<?php

require_once dirname(__FILE__) . '/../ObjectView.class.php';

class HTMLObjectView extends ObjectView
{
  var $controller;
  var $layout;
  var $link_view;
  var $config;
  var $fields_rendering_visitor;

    /* Initialization */

    function HTMLObjectView() {
        $this->fields_rendering_visitor =& new FieldsRenderingVisitor($this);
    }

    function setController(&$controller) {
        $this->config =& $controller->config;
        $this->controller =& $controller;
        $this->model =& $controller->model;
        $this->layout =& $this->config->getLayout();
        $this->link_view =& $this->config->getLinkView();
    }

    /* Rendering */


    function beginObjectRendering(&$html) {
        $this->triggerEvent('about_to_begin_object_rendering', array('view' => $this,
                                                                     'html' => $html));
        $this->layout->beginObjectRendering($this->model, $html);
    }

    function endObjectRendering(&$html) {
        $this->layout->beginObjectRendering($this->model, $html);
        $this->triggerEvent('object_rendered', array('view' => $this,
                                                     'html' => $html));
        $this->renderActions($html);
    }

    function beginFieldRendering(&$field, &$html) {
    	$this->triggerEvent('about_to_begin_field_rendering', array('view' => $this,
                                                                    'html' => $html,
                                                                    'field' => $field));
        $this->layout->beginFieldRendering($field, $html);
    }

    function endFieldRendering(&$field, &$html) {
        $this->layout->endFieldRendering($field, $html);
        $this->triggerEvent('field_rendered', array('view' => $this,
                                                    'html' => $html,
                                                    'field' => $field));

    }

    function beginObjectTitleRendering(&$html) {

    }

    function endObjectTitleRendering(&$html) {}

    /* Fields rendering */

    function renderField(&$field, $html) {
        $html->text($field->name . ': ');
        $this->fields_rendering_visitor->renderField(&$field, &$html);
    }

    function renderFields(&$html) {
        foreach ($this->model->fields as $field) {
            $this->renderField($field, $html);
        }
    }

    /* Actions rendering */

    function renderActions(&$html) {
    	$this->controller->renderActions($html);
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

class FieldsRenderingVisitor
{
    var $component;
    var $html;

    function FieldsRenderingVisitor(&$component) {
        $this->component =& $component;
    }

    function renderField(&$field, &$html) {
    	$this->html =& $html;
        $field->visit($this);
    }

    function visitedCollectionField(&$field) {
        $this->component->renderCollectionField($field, $this->html);
    }

    function visitedTextfield(&$field) {
    	$this->component->renderTextField($field, $this->html);
    }

    function visitedIdField(&$field) {
    	$this->component->renderIdField($field, $this->html);
    }
}


?>