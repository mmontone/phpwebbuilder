<?php

require_once dirname(__FILE__) . '/TemplateModel.class.php';
require_once pwbdir . '/lib/PHPTemplate.class.php';

class PHPTemplateModel extends TemplateModel
{
  var $template_model;

  function PHPTemplateModel() {
    $this->template_model =& new PHPTemplate();
    assert(defined('phptemplates_dir'));
    $this->template_model->set_path(phptemplates_dir);
  }

  function set($key, $value) {
    $this->template_model->set($key, $value);
  }

  function execute($template) {
    //var_dump($this->template_model);
    return $this->template_model->fetch($template);
  }
}


?>