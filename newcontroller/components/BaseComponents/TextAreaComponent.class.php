<?php

require_once dirname(__FILE__) . '/FormComponent.class.php';

class TextAreaComponent extends FormComponent{
	function createNode(){
		parent::createNode();
		$this->view->setTagName('textarea');
	}

	function prepareToRender(){
		$this->view->append_child(new XMLTextNode($this->value_model->getValue()));
	}
	function valueChanged(&$value_model, &$params) {
		/*WARNING!!! If there's an error, look here first ;) */
		$text = & $this->value_model->getValue();
		$new_view = $this->view;
		$this->view->replace_child(new XMLTextNode($text), $this->view->first_child());
		$this->view->parentNode->replace_child($this->view, $new_view);
	}
}

?>