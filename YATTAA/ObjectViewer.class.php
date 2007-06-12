<?php

class ObjectViewer extends ObjectPresenter {
	function chooseFieldDisplayer(&$field){
		return mdcall('getFieldViewer', array(&$field));
	}
}

#@defmdf &getFieldViewer(&$field: DataField)
{
	$t =& new Text($field);
	return $t;
}
//@#

#@defmdf &getFieldViewer(&$field: IndexField)
{
	$lab =& new Label($field->asTextHolder());
	return $lab;
}
//@#

#@defmdf &getFieldViewer(&$field: TextArea)
{
	$textArea =& new TextAreaComponent($person->observations);
	$textArea->disable();
	return $textArea;
}
//@#
#@defmdf &getFieldViewer(&$field: CollectionField)
{
	$on =& new ObjectsNavigator($field->collection);
	return $on;
}
//@#
?>