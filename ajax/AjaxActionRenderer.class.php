<?php

require_once dirname(__FILE__) . '/../templates/ActionRenderer.class.php';

class AjaxActionRenderer extends ActionRenderer
{
  function render(&$action) {
    //return "javascript: callAjax('" . ereg_replace('&', '&amp;', $action->href()) . "');";
    return "javascript: callAjax('" . $action->href() . "');";
  }
}

?>