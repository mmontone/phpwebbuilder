<?

require_once dirname(__FILE__) . "/../Links/MixLinker.class.php";
require_once(dirname(__FILE__) . "/../Action/TableCheckAction.class.php");
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
				$actunique = array();
				$res =& $db->SQLExec("SHOW INDEX FROM $table", FALSE,$this);
				$indexes = $db->fetchArray($res);
				foreach ($indexes as $f) {
					if ($f["Key_name"]!="PRIMARY") {
						$actunique []= $f["Column_name"];
					}
				}
				$a = count(array_diff($actunique,$this->obj->indexFields));
				$b = count(array_diff($this->obj->indexFields,$actunique));
				if ($a+$b>0){
					$ex=false;
					foreach($indexes as $ind){
						$ex |= $ind["Key_name"]=="index$table";
					}
					if ($ex){
						$temp .= "\n   DROP KEY index$table, ";
					}
					$temp .= "\n   ADD ".$this->uniques().", ";
				}
				if ($temp!="") {
					//$ret .= "\n-- Object: ".get_class($this->obj);
					$ret .= "\nALTER TABLE $table";
					$ret .= $temp;
					$ret = substr($ret,0, -2);
					$ret .= ";";
				}
		/*
		Si est�, hay que verificar los atributos y el tipo. Si no, hay que crearla.
		*/
			} else {
			//Si no est�, crearla:
				//$ret = "\n-- Object: ".get_class($this->obj);
				$ret .=	"\nCREATE TABLE IF NOT EXISTS $table (" ;
				$ret .= $this->fieldsForm(new MixLinker, $this->obj->fieldNames, TRUE);
				$ret .= "\n   PRIMARY KEY  (`id`)";
				$u = $this->uniques();
				if ($u) {
					$ret .= ", ".$u;
				}
				$ret .= "\n".
				$ret .= "\n);";
			/*faltan los campos!*/
			}
		} else {
			$ret="";
		}
		return $ret;
	}
	function uniques() {
		$table = $this->obj->tablename();
		$ifs =& $this->obj->findIndexField();
		foreach ($this->obj->indexFields as $i){
			$f =& $ifs[$i];
			$tc =& new TableCheckAction;
			$df =& $tc->viewFor($f);
			$uni .= "`".$i."`".$df->unique() . ", ";
		}
		$uni = substr($uni,0, -2);
		if (trim($uni)!="") {
			return  " UNIQUE index$table(".$uni.")";
		} else{
			return "";
		}
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
