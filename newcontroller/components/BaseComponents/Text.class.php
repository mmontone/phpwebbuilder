<?php

require_once dirname(__FILE__) . '/FormComponent.class.php';

class Text extends FormComponent{
	function Text (&$text){
		parent::FormComponent();
		$this->value =& $text;
	}
	function setText(&$text){
		$this->value =& $text;
	}
	function prepareToRender(){
		$this->view->text=&$this->value;
	}
	function &createDefaultView(){
		$this->view =& new HTMLTextNode($this->value, $this);
		return $this->view;
	}
	function printTree(){
		return $this->value;
	}

	function changed() {
		$new_view =& new HTMLTextNode($this->value, $this);
		// No se porque falla este assert
		assert($this->view->parentNode);
		$this->view->parentNode->replace_child($this->view, $new_view);
	}
}

?>