<?php

require_once dirname(__FILE__) . '/Widget.class.php';

class ActionLink extends Widget
{
	var $target;
	var $action;
	var $params;
	var $token;
	function ActionLink (&$target, $action, $text, &$params) {
		parent::Widget($vm = null);

		$this->target =& $target;
		$this->action = $action;
		$this->params = &$params;
		$this->addComponent(new Label($text), "linkName");
		$this->onClickSend('execute', $this);
	}
	function initializeDefaultView(&$view){
		$view->setTagName('a');
	}
	function initializeView(&$view){
		$this->app->historylistener->getToken($this);
	}
	function setToken($token){
		$this->token=$token;
		$this->view->setAttribute('href', 'link_dispatch.php?app='.getClass($this->app).'&amp;token='.$token);
	}
	function execute(){
		$action = $this->action;
		$this->target->$action($this->params);
	}
	function setOnClickEvent(&$view){
		parent::setOnClickEvent($view);
		$view->setAttribute('onclick', $view->getAttribute('onclick').'var ev = getEvent(event); ev.returnValue=false;return false;');
	}
}

class ActionLink2 extends ActionLink
{
	var $action;
	var $text;
	function ActionLink2 ($spec) {
		$this->action =& $spec['action'];

		if (is_string($spec['text']))
			$this->text =& new ValueHolder($spec['text']);
		else
			$this->text =& $spec['text'];

		$this->addComponent(new Text($this->text), "linkName");
		$this->onClickSend('execute', $this);
	}

	function initializeView(&$view){
		$view->setTagName('a');
	}

	function execute() {
		$this->action->call();
	}
}
?>