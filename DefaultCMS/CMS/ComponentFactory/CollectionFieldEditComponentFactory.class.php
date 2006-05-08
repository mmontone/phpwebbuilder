<?php

require_once 'EditComponentFactory.class.php';

class CollectionFieldEditComponentFactory extends EditComponentFactory {
	function &componentForField(&$field){
		return new ShowCollectionComponent(
			$field->collection
		);
	}
}


?>