<?php

require_once dirname(__FILE__) . '/Request.class.php';

class Action extends Request
{
  var $url;
  var $controller;
  var $action_selector;
  var $renderer;

  function Action($controller, $action_selector) {
    $this->url = pwb_url . '/Action.php';
    $this->controller = $controller;
    $this->renderer =& new SimpleActionRenderer();
    if ($action_selector == null)
      $action_selector = 'start';
    $this->action_selector = $action_selector;
  }

  function href() {
    $href = parent::href();
    $href .= '&amp;Controller=' . $this->controller;
    $href .= '&amp;action=' . $this->action_selector;
    return $href;
  }

  function render() {
    return $this->renderer->render($this);
  }
}

?>
