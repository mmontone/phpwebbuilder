<?php

class AjaxResponse
{
  var $actions;

  function AjaxResponse() {
    $this->actions = array();
  }

  function addAction(&$action) {
    $this->actions[] =& $action;
  }

  function replace($target_node, $html) {
    $this->addAction(new AjaxReplaceAction($target_node, $html));
  }

  function add($target_node, $html) {
    $this->addAction(new AjaxAddAction($target_node, $html));
  }

  function remove($target_node) {
      $this->addAction(new AjaxRemoveAction($target_node));
  }

  function render() {
    header("Content-type: text/xml");
    $xml = '<?xml version="1.0" encoding="ISO-8859-1" ?>';
    $xml .= "\n<ajax_response>";
    foreach ($this->actions as $action) {
      $xml .= toXML($action->render());
    }
    $xml .= "</ajax_response>";
    echo $xml;
    exit;
  }
}


class AjaxAction
{
  var $target_node;

  function AjaxAction($target_node) {
    $this->target_node = $target_node;
  }

}

class AjaxReplaceAction extends AjaxAction
{
  var $replacement;
  function AjaxReplaceAction($target_node, $replacement) {
    parent::AjaxAction($target_node);
    $this->replacement = $replacement;

  }
  function render() {
    $xml = "<replace id=\"" . $this->target_node . "\">";
    $xml .= $this->replacement;
    $xml .= "</replace>";
    return $xml;
  }
}

class AjaxAddAction extends AjaxAction
{
  var $addition;
  function AjaxReplaceAction($target_node, $addition) {
    parent::AjaxAction($target_node);
    $this->addition = $addition;
  }

  function render() {
    $xml = "<add id=" . $this->target_node . ">";
    $xml .= $this->addition;
    $xml .= "</add>";
    return $xml;
  }
}

class AjaxRemoveAction extends AjaxAction
{
  function render() {
    return "<remove id=" . $this->target_node . "/>";
  }
}


?>