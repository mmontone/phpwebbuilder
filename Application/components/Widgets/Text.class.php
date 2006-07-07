<?php
require_once dirname(__FILE__) . '/Widget.class.php';

class Text extends Widget {
	function Text(& $string_holder) {
		parent :: Widget($string_holder);
		if (is_string($string_holder)) {
			print_backtrace();
		}
	}

	function valueChanged(& $value_model, &$params) {
		/*WARNING!!! If there's an error, look here first ;) */
		if ($this->view){
			$text = & $this->value_model->getValue();
			$this->view->removeChilds();
			$this->view->appendChild(new XMLTextNode($text));
			$this->view->redraw();
		}
	}

	function & createDefaultView() {
		$t =& new XMLNodeModificationsTracker('span');
		return $t;
	}
	function prepareToRender(){
		$text = $this->value_model->getValue();
		$this->view->removeChilds();
		$this->view->appendChild(new XMLTextNode($text));
	}
	function printTree() {
		return $this->value_model->getValue();
	}
}
?>