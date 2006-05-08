<?php
require_once dirname(__FILE__) . '/FormComponent.class.php';

class Text extends FormComponent {
	function Text(& $string_holder) {
		parent :: FormComponent($string_holder);
		if (is_string($string_holder)) {
			print_backtrace();
		}
	}

	function valueChanged(& $value_model, & $params) {
		$text = & $this->value_model->getValue();
		$new_view =& new XMLNodeModificationsTracker('span');
		$new_view->controller =& $this;
		if ($text != '')
			$new_view->append_child(new XMLTextNode($text));
		$this->view->parentNode->replace_child($new_view, $this->view);
		$this->view =& $new_view;

		/*
		if ($text != '') {
			$new_text_node =& new XMLTextNode($text);

			if ($this->text_node == null) {
				$this->view->append_child($new_text_node);
			}
			else {
				$this->view->replace_child($new_text_node, $this->text_node);
			}
			$this->text_node =& $new_text_node;
		}
		else {
			if ($this->text_node != null)
				$this->view->remove_child($this->text_node);
		}
		*/
	}

	function & createDefaultView() {
		$this->view = & new XMLNodeModificationsTracker('span');
		$this->view->controller = & $this;
		$text = $this->value_model->getValue();

		if ($this->value_model->getValue() != '') {
			$this->view->append_child(new XMLTextNode($text));
		}

		return $this->view;
	}

	function printTree() {
		return $this->value_model->getValue();
	}
}
?>