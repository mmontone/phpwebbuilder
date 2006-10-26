<?

class PersistentObjectTableCheckView {
	var $gotFields;
	function &fieldsMap(&$fields){  /* la variable indica si los campos que referencian a otros objetos se incluyen*/
		$ret = array();
		$obj =& $this->obj;
		$fs =& $obj->getFields($fields);
		for ($i=0; $i<count($fs) ; $i++) {
			$field =& $fs[$i];
			/*
			$showField =& $this->fieldShowObject($field);
			$ret[$field->colName]=& $showField->showMap($this);*/
			$c =& new TableCheckAction;
			$showField =& $c->viewFor($field);
			$ret[$field->colName] =& $showField->showMap($this);
		}
		return $ret;
 	}

 	function fieldsForm(&$linker, &$fields, $objFields){  /* la variable indica si los campos que referencian a otros objetos se incluyen*/
		$ret = "";
		$obj =& $this->obj;
		$fs =& $obj->getFields($fields);
		for ($i=0; $i<count($fs) ; $i++) {
			$field =& $fs[$i];
			/*
			$showField =& $this->fieldShowObject($field);*/
			$c =& new TableCheckAction;
			$showField =& $c->viewFor($field);
			$ret .= $showField->show($this, $linker, $objFields);
		}
		return $ret;
 	}

	function show () {
		$table = $this->obj->getTable();
		$sql = "SHOW TABLES FROM `" . basename . "` LIKE '" . $table ."'";
		$db =& DBSession::Instance();
		$res = $db->SQLexec($sql, FALSE, $this->obj);
		if (strcasecmp(getClass($this->obj),"ObjSQL")!=0) {
			trace("Inspecting object ". getClass($this->obj));
			if(mysql_num_rows($res)) {
				trace("Tables with name $table are" . mysql_num_rows($res));
				$ret ="";
				//$sql = "SHOW COLUMNS FROM `" . $table."`";
				$sql = $db->driver->showColumnsFromTableSQL($table);
				$res = $db->query($sql);
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
						//$temp .= "\n    DROP COLUMN `$name`, ";
						$temp .= "\n     " . $db->driver->dropColumnSQL($name) . ", ";
					}
				}
				$actunique = array();
				$res =& $db->SQLExec("SHOW INDEX FROM `$table`", FALSE,$this);
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
					$us = $this->uniques();
					if ($us!=''){
						$temp .= "\n   ADD ".$us.", ";
					}
				}
				if ($temp!="") {
					//$ret .= "\n-- Object: ".getClass($this->obj);
					$ret .= "\nALTER TABLE `$table`";
					$ret .= $temp;
					$ret = substr($ret,0, -2);
					$ret .= ";";
				}
		/*
		Si est�, hay que verificar los atributos y el tipo. Si no, hay que crearla.
		*/
			} else {
			//Si no est�, crearla:
				//$ret = "\n-- Object: ".getClass($this->obj);
				$ret =	"\nCREATE TABLE IF NOT EXISTS `$table` (" ;
				$ret .= $this->fieldsForm($this, $this->obj->fieldNames, TRUE);
				$ret .= "\n   PRIMARY KEY  (`id`)";
				$u = $this->uniques();
				if ($u) {
					$ret .= ", ".$u;
				}
				$ret .= "\n";
				$ret .= "\n) " . $db->driver->tablePropertiesSQL() .";";
			}
		} else {
			$ret="";
		}
		return $ret;
	}
	function uniques() {
		$table = $this->obj->getTable();
		$ifs =& $this->obj->allIndexFields();
		$unis=array();
		foreach ($this->obj->indexFields as $i){
			$f =& $ifs[$i];
			$tc =& new TableCheckAction;
			$df =& $tc->viewFor($f);
			$unis []= $df->createUnique($i);
		}
		$uni = implode(', ',$unis);
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
				$ret = "\n    MODIFY `$name` ".$field->type().", ";
			} else $ret = "";
			return $ret;
		} else {
			trace(print_r($field, TRUE));
			$add = "\n    ADD COLUMN `$name` ". $field->type().", ";
			return $add;
		}
	}
}

class TablesChecker {
	function checkTables($stepping=false){
		$arr = get_subclasses("PersistentObject");
		/*Comparing existing tables, existing objects, and added objects*/
		/*If a table has not an object table, we have to delete it*/
		$sql = "SHOW TABLES FROM `" . basename. '` LIKE \''.baseprefix.'%\'';
		$db =& DBSession::Instance();
		$res = $db->SQLexec($sql, FALSE, $this->obj);
		$tbs = $db->fetchArray($res);
		$tables= array();
		foreach($tbs as $t){
			foreach($t as $tname){
				$tables[$tname] = $tname;
			}
		}
		foreach ($arr as $o) {
			$obj = new $o;
			$dbc = new PersistentObjectTableCheckView;
			$dbc->obj=$obj;
			$mod .= $dbc->show();
			$table = $obj->getTable();
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
