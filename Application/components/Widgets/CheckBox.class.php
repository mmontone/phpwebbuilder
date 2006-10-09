<?php
class CheckBox extends Widget {
	var $disabled = false;

	function CheckBox(& $boolHolder) {
		parent :: Widget($boolHolder);
	}

	function valueFromForm(& $params) {
		return $params == '1';
	}
}
 ?>