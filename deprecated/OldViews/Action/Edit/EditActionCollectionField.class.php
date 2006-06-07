<?php

class EditActionCollectionField extends EditAction {
    function showField ($object) {
		$colec =& $this->field->collection;
        $view =& $object->viewFor($colec);
		$ret =& $view->showElements();
		$htmlobj =& $object->viewFor(new $colec->dataType);
		$ret .= $htmlobj->makelink("Agregar", "Add", "&".$colec->dataType.$this->field->fieldname."=".$object->obj->getID());
		return $ret;
	}
}

?>
