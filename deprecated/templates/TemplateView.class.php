<?php

require_once dirname(__FILE__) . '/../OldViews/View.class.php';

class TemplateView extends View
{
  var $template_model;
  var $template;

  function TemplateView($params) {
    $params['template_model'] = 'PHPTemplateModel';
    $this->initialize($params);
    $this->template_model =& new $params['template_model'];
    assert($params['template']);
    $this->template = $params['template'];
  }

  function initialize(&$params) {}

  function render($out=null) {
    $this->template_model->set('model', $this->controller->model);
    $this->controller->dispatchActions($this);
    //    var_dump($this->template_model);
    //exit;
    $this->template_model->set('controller', $this->controller);
    return $this->template_model->execute($this->template);
  }

  function setAction($name, &$action) {
    //assert($this->template);
    //$action->set('template', $this->template);
    $this->template_model->set($name, $action->render());
  }

  function set($key, $value) {
    $this->template_model->set($key, $value);
  }

  function get($key) {
    return $this->template_model->get($key);
  }
}

?>