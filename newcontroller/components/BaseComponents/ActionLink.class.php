<?php

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
		if (!$link) print_backtrace();
		$link->setAttribute('onclick', 'callAction(&#34;'.$this->getId().'&#34;);');
	}
	function viewUpdated($exec){
		$act = $this->act;
		$this->obj->$act($this->params);
	}
}

?>