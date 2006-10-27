<?

class TableCheckAction {
	var $field;

	function &viewFactory(&$obj) {
		$ok = false;
		$c = getClass($obj);
		while(!$ok && $c!='') {
			$name = $c.getClass($this);
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
		return "\n   `".$this->obj->colName ."` ". $this->type().", ";
	}
	function frmName(&$object) {
		return $object->formName() . $this->obj->colName;
	}
	function type(){
		trace ("Error: ".print_r($this,TRUE));
	}
	function compareType ($t) {
		$b = eregi($this->typeEreg(), $t);
		trace("en ". $this->obj->colName." ".	$t.
			" es ".	($b?"igual":"<b>diferente</b>")." a ".
			$this->type() . "<br>");
		return $b;
	}
	function typeEreg(){
		return "^".$this->type()."$";
	}
	function unique(){}
	function createUnique($i){
		return  "`".$i."`".$this->unique();
	}
}

class CollectionFieldTableCheckAction extends TableCheckAction {
	function show ($object, $linker, $objFields) {
		return "";
	}
	function showMap ($object, $objFields=array()) {
		return "";
	}
	function createUnique($i){return '';}
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
		$nt = $this->obj->creationParams['numtype'];
		if ($nt != '' && $nt != 'int') {
			return "float";
		} else {
			return "int(11)";
		}
	}
	function typeEreg(){
		$nt = $this->obj->creationParams['numtype'];
		if ($nt != '' && $nt != 'int') {
			return "float";
		} else {
			return "^int\([0-9]*\)";
		}
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
class EmailFieldTableCheckAction extends TextFieldTableCheckAction {}

class IdFieldTableCheckAction extends NumFieldTableCheckAction {
	function type(){
		return "int(11) unsigned NOT NULL AUTO_INCREMENT";
	}
}

class VersionFieldTableCheckAction extends NumFieldTableCheckAction {
	function type(){
		return "int(11) unsigned NOT NULL DEFAULT 0";
	}
}

class SuperFieldTableCheckAction extends NumFieldTableCheckAction {
	function type(){
		return "int(11) unsigned";
	}

}

class FilenameFieldTableCheckAction extends TextFieldTableCheckAction {}
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
