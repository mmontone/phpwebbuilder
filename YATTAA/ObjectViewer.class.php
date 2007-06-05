<?php

class ObjectViewer extends ObjectPresenter {
	function chooseFieldDisplayer(&$field){
		return mdcall('getFieldViewer', array(&$field));
	}
}

#@defmdf &getFieldViewer(&$field: DataField)
{
	return new Text($field);
}
//@#

#@defmdf &getFieldViewer(&$field: IndexField)
{
	return new Label($field->asTextHolder());
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
	return new ObjectsNavigator($field->collection);
}
//@#
?>