<?php

class ObjectViewer extends ObjectPresenter {
	function chooseFieldDisplayer(&$field){
		return mdcompcall('getFieldViewer', array(&$this,&$field));
	}
}

#@defmdf &getFieldViewer[Component](&$field: DataField)
{
	$t =& new Text($field);
	return $t;
}
//@#

#@defmdf &getFieldViewer[Component](&$field: IndexField)
{
	$lab =& new Text($field->asTextHolder());
	return $lab;
}
//@#

#@defmdf &getFieldViewer[Component](&$field: TextArea)
{
	$textArea =& new TextAreaComponent($field);
	$textArea->disable();
	return $textArea;
}
//@#

#@defmdf &getFieldViewer[Component](&$field: BoolField)
{
	$checkbox =& new CheckBox($field);
	$checkbox->disable();
	return $checkbox;
}
//@#


#@defmdf &getFieldViewer[Component](&$field: CollectionField)
{
	$on =& new ObjectsNavigator($field->getCollection());
	return $on;
}
//@#
?>