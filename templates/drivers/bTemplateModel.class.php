<?php

require_once dirname(__FILE__) . '/../../lib/bTemplate.php';
require_once dirname(__FILE__) . '/TemplateModel.class.php';

class bTemplateModel extends TemplateModel
{
  var $template_model;

  function bTemplateModel() {
    $this->template_model =& new bTemplate();
  }

  function set($key, $value) {
    $this->template_model->set($key, $value);
  }

  function execute($template) {
    $this->template_model->fetch($template);
  }
}


 ?>