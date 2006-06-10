<?

class TableCheckAction {
	var $field;

	function &viewFactory(&$obj) {
		$ok = false;
		$c = get_class($obj);
		while(!$ok) {
			$name = $c.get_class($this);
			$ok = class_exists($name);
			$c = get_parent_class($c);
		}
		$v=& new $name;
		$v->obj =& $obj;
		return $v;
	}
	function &viewFor(&$field){
		return $this->viewFactory($field);
	}
	function showMap(&$obj) {
		return $obj->showFieldMap($this);
	}
	function show(&$obj, &$linker, $showObjFields) {
		return $obj->showField($this);
	}
	function creation(&$obj) {
		return "\n   ".$this->obj->colName ." ". $this->type().", ";
	}
	function frmName(&$object) {
		return $object->formName() . $this->field->colName;
	}
	function type(){
		trace ("Error: ".print_r($this,TRUE));
	}
	function visitedTextField($field) {
		$view = new TableCheckTextFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedEmailField($field) {
		$view = new TableCheckEmailFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedPasswordField($field) {
		$view = new TableCheckPasswordFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedDateTimeField($field) {
		$view = new TableCheckDateTimeFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedDateField($field) {
		$view = new TableCheckDateFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedTimeField($field) {
		$view = new TableCheckTimeFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedCollectionField($field) {
		$view = new TableCheckCollectionFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedTextArea($field) {
		$view = new TableCheckTextAreaView;
		$view->field = $field;
		return $view;
	}
	function visitedBoolField($field) {
		$view = new TableCheckBoolFieldView	;
		$view->field = $field;
		return $view;
	}
	function visitedNumField($field) {
		$view = new TableCheckNumFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedEnumField($field) {
		$view = new TableCheckEnumFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedIdField($field) {
		$view = new TableCheckIdFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedIndexField($field) {
		$view = new TableCheckIndexFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedUserField($field) {
		$view = new TableCheckUserFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedEmbedField($field) {
		$view = new TableCheckEmbedFieldView;
		$view->field = $field;
		return $view;
	}
	function compareType ($t) {
		$b = eregi($this->typeEreg(), $t);
		trace("en ". $this->field->colName." ".	$t.
			" es ".	($b?"igual":"<b>diferente</b>")." a ".
			$this->type() . "<br>");
		return $b;
	}
	function typeEreg(){
		return "^".$this->type()."$";
	}
	function unique(){}
}

class CollectionFieldTableCheckAction extends TableCheckAction {
	function show ($object, $linker, $objFields) {
		return "";
	}
	function showMap ($object, $objFields=array()) {
		return "";
	}
}
class TableCheckEnumFieldView extends TableCheckAction {
	function type (){
		return "TEXT";
	}
}
class EmbedFieldTableCheckAction extends TableCheckAction {}
class PasswordFieldTableCheckAction extends TextFieldTableCheckAction {
	function unique(){}
	function type (){
		return "TEXT";
	}
}

class NumFieldTableCheckAction extends TableCheckAction {
	function type (){
		return "int(11)";
	}
	function typeEreg(){
		return "^int\([0-9]*\)";
	}
}

class IndexFieldTableCheckAction extends NumFieldTableCheckAction {}
class UserFieldTableCheckAction extends IndexFieldTableCheckAction {}


class TextFieldTableCheckAction extends TableCheckAction {
	function unique(){
		return "(50)";
	}
	function type (){
		return "TEXT";
	}
}
class EmailFieldTableCheckAction extends TextFieldTableCheckAction {
	function type (){
		return "TEXT";
	}
}

class IdFieldTableCheckAction extends NumFieldTableCheckAction {
	function type(){
		return "int(11) unsigned NOT NULL auto_increment";
	}
}

class SuperFieldTableCheckAction extends NumFieldTableCheckAction {
	function type(){
		return "int(11) unsigned";
	}

}

class FilenameFieldTableCheckAction extends TableCheckAction {
	function type (){
		return "TEXT";
	}
}
class BlobFieldTableCheckAction extends TableCheckAction {
	function type (){
		return "LONGBLOB";
	}
}
class TextAreaTableCheckAction extends TextFieldTableCheckAction {
	function type (){
		return "LONGTEXT";
	}
}

class BoolFieldTableCheckAction extends NumFieldTableCheckAction {
	function type (){
		return "int(1)";
	}
}

class DateTimeFieldTableCheckAction		extends TableCheckAction {
	function type (){
		return "datetime";
	}
}

class DateFieldTableCheckAction			extends DateTimeFieldTableCheckAction {
	function type (){
		return "date";
	}
}

class TimeFieldTableCheckAction			extends DateTimeFieldTableCheckAction {
	function type (){
		return "time";
	}
}
