<?php

require_once dirname(__FILE__) . '/Component.class.php';

class FlowController extends Component
{
	var $model;
	var $view;
    var $config;
    var $action_renderer;

    function FlowController(&$model, &$view, &$config) {
      parent::Component($model, $view, $config);
      $this->model =& $model;
	$this->view =& $view;
        $this->config =& $config;
        $this->action_renderer =& $config->getActionRenderer();
        $this->view->setController($this);
	}

	function render_on(&$html) {
	    $this->view->render_on($html);
	}

    function &getModel() {
        return $this->model;
    }
}
?>