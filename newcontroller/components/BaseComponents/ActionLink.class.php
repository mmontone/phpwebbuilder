<?php

require_once dirname(__FILE__) . '/FormComponent.class.php';
require_once dirname(__FILE__) . '/FormComponent.class.php';

class ActionLink extends FormComponent{
	var $obj;
	var $act;
	var $link;
	var $params;
	function ActionLink (&$obj,$act, $link, &$params){
		parent::FormComponent();
		$this->obj =& $obj;
		$this->act=$act;
		$this->link = $link;
		$this->params = &$params;
		$this->add_component(new Text($link), "linkName");
	}
	function createNode(){
		$link =& $this->view;
		$link->setTagName('a');
	}
	function prepareToRender(){
		parent::prepareToRender();
		$link =& $this->view;
		$app =& Application::instance();
		$action = $app->page_renderer->renderActionLinkAction($this);
		$link->setAttribute('onclick', $action);
	}

	function viewUpdated($params){
		$act = $this->act;
		$this->obj->$act($this->params);
	}

}


?>
