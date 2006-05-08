<?php

require_once 'EditComponentFactory.class.php';

class IdFieldEditComponentFactory extends EditComponentFactory {
	function &componentForField(&$field){
		return new Text(new ValueHolder($field->value));
	}
}


?>