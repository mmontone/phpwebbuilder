<?php

require_once dirname(__FILE__) . '/../../Component.class.php';

class FormComponent extends Component
{
	var $value;
	function FormComponent($callback_actions=array('on_accept' => 'notification_accepted')) {
		parent::Component($callback_actions);
	}
	function declare_actions(){return array();}
	function viewUpdated ($params){
		if ($params!=$this->value){
			$this->setValue($params);
			$this->triggerEvent('changed');
		}
	}
	function &createDefaultView(){
		$this->view =& new HTMLRendererNew;
		$this->createNode();
		return $this->view;
	}
	function createNode(){}
}

?>