<?php

//require_once smarty_dir . '/Smarty.class.php';
require_once dirname(__FILE__) . '/TemplateModel.class.php';

class SmartyTemplateModel extends TemplateModel
{
  var $smarty;

  function SmartyTemplateModel() {
    $this->smarty =& new Smarty();
    $this->smarty->template_dir = smarty_user_dir . '\templates';
    $this->smarty->compile_dir = smarty_user_dir . '\templates_c';
    $this->smarty->cache_dir = smarty_user_dir . '\cache';
    $this->smarty->config_dir = smarty_user_dir . '\configs';
  }

  function set($key, &$value) {
    $this->smarty->assign($key, $value);
  }

  function execute($template) {
    //    $vars = $this->smarty->get_template_vars();
    //print_r($vars);
    $this->smarty->display($template);
  }
}


?>