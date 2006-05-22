<?php

require_once dirname(__FILE__) . '/Edit/EditComponentFactory.class.php';

class BoolFieldEditComponentFactory extends EditComponentFactory {
	function &componentForField(&$field){
		return new CheckBox(new ValueHolder($field->value));
	}
}


?>