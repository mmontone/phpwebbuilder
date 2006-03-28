<?php

require_once dirname(__FILE__) . '/Component.class.php';

class FlowController extends Component
{
	var $model;
	var $view;

	function Controller(&$model, &$view) {
		$this->model =& $model;
		$this->view =& $view;
        $this->view->controller =& $this;
	}

	function renderOn(&$out) {
		$this->view->renderOn($out);
	}

    function &getModel() {
        return $this->model;
    }
}
?>