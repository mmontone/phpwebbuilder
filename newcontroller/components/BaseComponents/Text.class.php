<?php

require_once dirname(__FILE__) . '/FormComponent.class.php';

class Text extends FormComponent{
	function Text ($text){
		parent::FormComponent();
		$this->value = $text; 
	}
	function setText($text){
		$this->value = $text;		
	}
	function prepareToRender(){
		$this->view->text=$this->value;	
	}
	function &createDefaultView(){
		$this->view =& new HTMLTextNode($this->value, $this);
		return $this->view;
	}
}

?>