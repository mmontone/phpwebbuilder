<?php

require_once dirname(__FILE__) . '/FormComponent.class.php';

class ActionLink extends FormComponent
{
	var $target;
	var $action;
	var $params;

	function ActionLink (&$target, $action, $text, &$params) {
		parent::FormComponent($vm = null);

		$this->target =& $target;
		$this->action = $action;
		$this->params = &$params;
		$this->addComponent(new Label($text), "linkName");
	}

	function createNode(){
		parent::createNode();
		$this->view->setTagName('a');
	}
	function prepareToRender(){
		parent::prepareToRender();
		$action = 'callAction(&#34;' . $this->getId() . '&#34;);';
		$this->view->setAttribute('onclick', $action);
	}
	function execute(){
		$action = $this->action;
		$this->target->$action($this->params);
	}
	function viewUpdated($p){
		if ($p=='execute'){
			$this->execute();
		}
	}

}

class ActionLink2 extends FormComponent
{
	var $action;
	var $text;
	function ActionLink2 ($spec) {
		parent::FormComponent($vm = null);

		$this->action =& $spec['action'];
		$this->text =& $spec['text'];
		$this->addComponent(new Text($this->text), "linkName");
	}

	function createNode(){
		parent::createNode();
		$this->view->setTagName('a');
	}

	function prepareToRender(){
		parent::prepareToRender();
		$link =& $this->view;
		$app =& $this->application();
		//$action = $app->page_renderer->renderActionLinkAction($this);
		$action = 'callAction(&#34;' . $this->getId() . '&#34;);';
		$link->setAttribute('onclick', $action);
	}
	function execute(){
		$this->action->call();
	}

	function viewUpdated($p){
		if ($p=="execute"){
			$this->execute();
		}
	}

}
?>