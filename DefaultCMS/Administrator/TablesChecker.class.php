<?

class ObjectMapper {
	var $gotFields;
	function &checkFields(&$fields){
		$ret = array();
		foreach (array_keys($fields) as $i) {
			$field =& $fields[$i];
			$tca =& new FieldMapper;
			$fieldMapper =& $tca->createFor($field);
			$ret[$field->colName] = $fieldMapper->findModifications($this);
		}
		return $ret;
 	}

 	function createFields(&$fields){  /* la variable indica si los campos que referencian a otros objetos se incluyen*/
		$ret = "";
		foreach (array_keys($fields) as $i) {
			$field =& $fields[$i];
			$c =& new FieldMapper;
			$fieldMapper =& $c->createFor($field);
			$ret .= $fieldMapper->creation($this);
		}
		return $ret;
 	}

	function analizeMods () {
		$table = $this->object->getTablePrefixed('');
		$sql = "SHOW TABLES FROM `" . basename . "` LIKE '" . $table ."'";
		$db =& DBSession::Instance();
		$res = $db->query($sql);
			if(mysql_num_rows($res)) {
				$arr = $db->fetchArray($res);
				$this->tableName=array_pop($arr[0]);
				$table = $this->tableName;
				$ret ="";
				$sql = $db->driver->showColumnsFromTableSQL($table);
				$res = $db->query($sql);
				$arr = $db->fetchArray($res);
				foreach ($arr as $f) {
					$arr2 [$f["Field"]]=$f;
				}
				$this->gotFields = $arr2;
				$arr = $this->checkFields($this->object->class->fieldsWithNames($this->object->class->fieldNames));
				$temp = '';
				foreach ($arr as $name=>$f) {
					$temp .= $f;
				}
				foreach ($this->gotFields as $name=>$f) {
					if (!isset($arr[$f["Field"]])) {
						$temp .= "\n     " . $db->driver->dropColumnSQL($name) . ", ";
					}
				}
				$actunique = array();
				$res =& $db->query("SHOW INDEX FROM `$table`");
				$indexes = $db->fetchArray($res);
				$has_super_unique = false;
				foreach ($indexes as $f) {
					if ($f["Key_name"]!="PRIMARY" && $f["Column_name"]!="super") {
						$actunique []= $f["Column_name"];
					}
					if ($f["Column_name"]=="super" && $f["Non_unique"]==0) {
						$has_super_unique = true;
					}
				}
				if (!$has_super_unique && isset($this->object->fieldNames['super'])) {
					$temp .= "\n   ADD UNIQUE (`super`), ";
				}
				$a = count(array_diff($actunique,$this->object->indexFields));
				$b = count(array_diff($this->object->indexFields,$actunique));
				if ($a+$b>0){
					$ex=false;
					foreach($indexes as $ind){
						$ex |= $ind["Key_name"]=="index$table";
					}
					if ($ex){
						$temp .= "\n   DROP KEY index$table, ";
					}
					$us = $this->uniques();
					if ($us!=''){
						$temp .= "\n   ADD ".$us.", ";
					}
				}
				if ($temp!="") {
					$ret .= "\nALTER TABLE `$table`";
					$ret .= $temp;
					$ret = substr($ret,0, -2);
					$ret .= ";";
				}
			} else {
				$this->tableName=$table;
				$ret =	"\nCREATE TABLE `".$table."` (" ;
				$ret .= $this->createFields($this->object->class->fieldsWithNames($this->object->class->fieldNames));
				$ret .= "\n   PRIMARY KEY  (`id`)";
				$u = $this->uniques();
				if ($u) {
					$ret .= ", ".$u;
				}
				$ret .= "\n";
				$ret .= "\n) " . $db->driver->tablePropertiesSQL() .";";
			}
		return $ret;
	}
	function uniques() {
		$table = $this->object->getTable();
		$ifs =& $this->object->allIndexFieldNames();
		$unis=array();
		foreach ($ifs as $i){
			$f =& $this->object->class->$i;
			$tca =& new FieldMapper;
			$df =& $tca->createFor($f);
			$unis []= $df->createUnique();
		}
		$uni = implode(', ',$unis);
		if (trim($uni)!="") {
			return  " UNIQUE index".$this->tableName."(".$uni.")";
		} else{
			return "";
		}
	}
	function findModifications(&$field){
		$name = $field->field->colName;
		$fields =& $this->gotFields;
		if (isset($fields[$name])) {
			$f =& $fields[$name];
			if (!$field->checkField($f)) {
				$ret = "\n    MODIFY `$name` ".$field->allType().", ";
			} else $ret = "";
			return $ret;
		} else {
			$add = "\n    ADD COLUMN `$name` ". $field->allType().", ";
			return $add;
		}
	}
}

class TablesChecker {
	function checkTables($stepping=false){
		$arr = get_subclasses("PersistentObject");
		reset_metadata();
		/*Comparing existing tables, existing objects, and added objects*/
		/*If a table has not an object table, we have to delete it*/
		$sql = "SHOW TABLES FROM `" . basename. '` LIKE \''.baseprefix.'%\'';
		$db =& DBSession::Instance();
		$res = $db->SQLexec($sql, FALSE, $n=null, $rows=0);
		$tbs = $db->fetchArray($res);
		$tables= array();
		foreach($tbs as $t){
			foreach($t as $tname){
				$tables[$tname] = $tname;
			}
		}
		$mod='';
		foreach ($arr as $o) {
			$obj =& PersistentObject::getMetaData($o);
			$dbc = new ObjectMapper;
			$dbc->object=&$obj;
			$obj->createObject();
			$mod .= $dbc->analizeMods();
			$obj->disposeObject();
			$table = $dbc->tableName;
			unset($tables[$table]);
			if ($mod!='' && $stepping) return $mod;
		}
		$del ='';
		foreach ($tables as $t2){
			$del .= "\nDROP TABLE `".$t2.'`;';

		}
		return $mod.$del;
	}
}

?>
