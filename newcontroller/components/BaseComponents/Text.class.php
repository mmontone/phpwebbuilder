<?php
require_once dirname(__FILE__) . '/FormComponent.class.php';

class Text extends FormComponent {
	function Text(& $string_holder) {
		parent :: FormComponent($string_holder);
		if (is_string($string_holder)) {
			print_backtrace();
		}
	}

	function initializeView(&$view) {}

	function valueChanged(& $value_model, & $params) {
		/*WARNING!!! If there's an error, look here first ;) */
		if ($this->view){
			$text = & $this->value_model->getValue();
			$new_view = $this->view;
			$this->view->remove_childs();
			$this->view->append_child(new XMLTextNode($text));
			$this->view->parentNode->replace_child($this->view, $new_view);
		}
	}

	function & createDefaultView() {
		return new XMLNodeModificationsTracker('span');
	}
	function prepareToRender(){
		$text = $this->value_model->getValue();
		$this->view->remove_childs();
		$this->view->append_child(new XMLTextNode($text));
	}
	function printTree() {
		return $this->value_model->getValue();
	}
}
?>