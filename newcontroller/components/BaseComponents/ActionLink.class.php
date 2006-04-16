<?php

require_once dirname(__FILE__) . '/../../Component.class.php';

class ActionLink extends FormComponent{
	var $obj;
	var $act;
	var $link;
	function ActionLink (&$obj,$act, $link, $params=array()){
		parent::FormComponent();
		$this->obj =& $obj;
		$this->act=$act; 
		$this->link = $link;
		$this->params = $params;
	}
	function createNode(){
		$link =& $this->view;
		$link->setTagName('a');
		$l =& $this->view->create_text_node($this->link, $this);
		$link->append_child($l);
	}
	function prepareToRender(){
		parent::prepareToRender();
		$link =& $this->view;
		$link->setAttribute('onclick', 'callAction(&#34;'.$this->getId().'&#34;);');
		$link->setAttribute('href', 'javascript:callAction(&#34;'.$this->getId().'&#34;);');
	}
	function viewUpdated($params){
		$act = $this->act; 
		$this->obj->$act($this->params);
	}
}

?>