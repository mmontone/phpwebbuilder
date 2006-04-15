<?php

require_once dirname(__FILE__) . '/FormComponent.class.php';

class Text extends FormComponent{
	function Text ($text){
		parent::FormComponent();
		$this->setText($text); 
	}
	function setText($text){
		$this->value = $text;
		$this->view->text=$text;
	}
	function &createDefaultView(){
		$this->view =& new HTMLTextNode($this->value, $this);
		return $this->view;
	}
}

?>