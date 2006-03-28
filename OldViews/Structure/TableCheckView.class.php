<?

require_once dirname(__FILE__) . "/../Links/MixLinker.class.php";
require_once(dirname(__FILE__) . "/../Action/TableCheckFieldView.class.php");
require_once("HtmlFormEditView.class.php");

class TableCheckView extends HtmlFormEditView  {
	function fieldShowObjectFactory () {
		return new TableCheckAction;
	}
	function visitedPersistentCollection ($obj) {
		$view = new PersistentCollectionTableCheckView;
		$view->obj = $obj;
		return $view;
	}
	function visitedPersistentObject ($obj) {
		$view = new PersistentObjectTableCheckView;
		$view->obj = $obj;
		return $view;
	}
}

class PersistentObjectTableCheckView extends TableCheckView  {
	var $gotFields;
	function show () {
		$table = $this->obj->tablename();
		$sql = "SHOW TABLES FROM " . basename . " LIKE '" . $table ."'";
		$db = new MySQLDB;
		$res = $db->SQLexec($sql, FALSE, $this->obj); 
		if (strcasecmp(get_class($this->obj),"ObjSQL")!=0) {
			trace("Inspecting object ". get_class($this->obj));
			if(mysql_num_rows($res)) {
				trace("Tables with name $table are" . mysql_num_rows($res));
				$ret ="";
				$sql = "SHOW COLUMNS FROM " . $table;
				$db = new MySQLDB;
				$res = $db->SQLexec($sql, FALSE, $this->obj); 
				$arr = $db->fetchArray($res);
				foreach ($arr as $f) {
					$arr2 [$f["Field"]]=$f;
				}
				$this->gotFields = $arr2;
				$arr = $this->fieldsMap($this->obj->fieldNames, TRUE);
				foreach ($arr as $name=>$f) {
					$temp .= $f;
				}
				trace("Fields:" . print_r($f, TRUE));				
				trace("Deleting Extra Fields");
				foreach ($this->gotFields as $name=>$f) {
					if (!isset($arr[$f["Field"]])) {
						trace("Field not found:" . print_r($f, TRUE));
						$temp .= "\n    DROP COLUMN $name, ";
					} 
				}
				if ($temp!="") {
					//$ret .= "\n-- Object: ".get_class($this->obj);
					$ret .= "\nALTER TABLE $table";
					$ret .= $temp;
					$ret = substr($ret,0, -2);
					$ret .= ";";
				} 
		/*
		Si está, hay que verificar los atributos y el tipo. Si no, hay que crearla. 
		*/	
			} else {
			//Si no está, crearla:
				//$ret = "\n-- Object: ".get_class($this->obj);
				$ret .=	"\nCREATE TABLE IF NOT EXISTS $table (" ;
				$ret .= $this->fieldsForm(new MixLinker, $this->obj->fieldNames, TRUE);
				$ret .= "\n   PRIMARY KEY  (`id`)";		
				$ret .= "\n);";
			/*faltan los campos!*/
			}
		} else {
			$ret="";
		}
		return $ret;
	}
	function showField(&$field){
		return $field->creation($this);
	}
	function showFieldMap(&$field){
		$name = $field->obj->colName;
		$fields =& $this->gotFields;
		if (isset($fields[$name])) {
			$f =& $fields[$name];
			if (!$field->compareType($f["Type"])) {
				$ret = "\n    MODIFY $name ".$field->type().", ";
			} else $ret = "";
			return $ret;
		} else {
			trace(print_r($field, TRUE));
			$add = "\n    ADD COLUMN $name ". $field->type().", ";
			return $add;
		}
	}
}
?>
