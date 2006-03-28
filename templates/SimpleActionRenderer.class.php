<?php

require_once dirname(__FILE__) . '/ActionRenderer.class.php';

class SimpleActionRenderer extends ActionRenderer
{
  function render($action) {
    return $action->href();
  }
}

?>