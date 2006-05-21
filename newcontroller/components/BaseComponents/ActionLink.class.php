<?php

require_once dirname(__FILE__) . '/FormComponent.class.php';

class ActionLink extends FormComponent
{
	var $target;
	var $action;
	var $text;
	var $params;

	function ActionLink (&$target, $action, $text, &$params) {
		parent::FormComponent($vm = null);
		$this->target =& $target;
		$this->action = $action;
		$this->text = $text;
		$this->params = &$params;
		$this->addComponent(new Text(new ValueHolder($this->text)), "linkName");
	}

	function createNode(){
		$link =& $this->view;
		$link->setTagName('a');
	}
	function prepareToRender(){
		parent::prepareToRender();
		$link =& $this->view;
		$app =& $this->application();
		$action = $app->page_renderer->renderActionLinkAction($this);
		$link->setAttribute('onclick', $action);
	}
	function execute(){
		$action = $this->action;
		$this->target->$action($this->params);
	}
	function viewUpdated($p){
		if ($p=="execute"){
			$this->execute();
		}
	}

}


?>