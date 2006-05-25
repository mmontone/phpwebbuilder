<?php

require_once 'EditComponentFactory.class.php';

class IndexFieldEditComponentFactory extends EditComponentFactory {
	function &componentForField(&$field) {
		return new IndexFieldChooser($field);
	}
}

?>