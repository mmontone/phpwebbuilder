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
	$lab =& new Text($field->asTextHolder());
	return $lab;
}
//@#

#@defmdf &getFieldViewer(&$field: TextArea)
{
	$textArea =& new TextAreaComponent($field);
	$textArea->disable();
	return $textArea;
}
//@#

#@defmdf &getFieldViewer(&$field: BoolField)
{
	$checkbox =& new CheckBox($field);
	$checkbox->disable();
	return $checkbox;
}
//@#


#@defmdf &getFieldViewer(&$field: CollectionField)
{
	$on =& new ObjectsNavigator($field->getCollection());
	return $on;
}
//@#
?>