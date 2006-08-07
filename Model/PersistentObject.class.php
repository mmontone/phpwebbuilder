<?

class PersistentObject extends DescriptedObject {
	var $table;

	function setID($id) {
		foreach ($this->allFieldNamesThisLevel() as $field) {
			$this->$field->setID($id);
		}
	}

	function getID() {
		return $this->id->getValue();
	}

    function getIdOfClass($class){
	    	if (strcasecmp(getClass($this), $class)==0) {
	    		return $this->id->getValue();
	    	} else {
	    		if ($this->parent==null) print_backtrace(getClass($this).' doesn have parent '.$class);
	    		return $this->parent->getIdOfClass($class);
	    	}
    }

	function fieldNames($operation) {
		$fieldnames = '';
		if ($operation=='SELECT'){
			$fs =& $this->allFieldsAllLevels();
		} else {
			$fs =& $this->allFieldsThisLevel();
		}
		foreach ($fs as $name => $field) {
			$fieldnames .= $field->fieldName($operation);
		}
		$fieldnames = substr($fieldnames, 0, -2);
		return $fieldnames;
	}

	function &allFieldsAllLevels(){
		$rcs = get_related_classes(getClass($this));
		$fs = array();
		foreach($rcs as $rc){
			if ($rc != 'persistentobject' && $rc != 'descriptedobject' && $rc != 'pwbobject') {
				$o = new $rc;
				$fs = array_merge($fs, $o->allSQLFields());
			}
		}
		$fs = array_merge($fs, $this->allSQLFields());
		return $fs;
	}

	function voidOption() {
		return $this->voidOption;
	}

	function tableName() {
		return baseprefix . $this->table;
	}

	function tableNames(){
		$tns[] = '`'.$this->tableName().'`';
		$p0 = getClass($this);
		$pcs = get_superclasses($p0);
		$o0 =& $this;
		foreach($pcs as $pc){
			$o1 =& new $pc;
			if ($pc != 'persistentobject' && $pc != 'descriptedobject' && $pc != 'pwbobject' && $pc != ''){
				$tns[] = 'LEFT OUTER JOIN `'.$o1->tableName().'` ON `'. $o1->tableName().'`.id = `'.$o0->tableName().'`.super';
			}
			$o0 =& $o1;
			$p0 = $pc;
		}
		$scs = get_subclasses(getClass($this));
		foreach($scs as $sc){
			$o1 =& new $sc;
			$pc = get_parent_class($sc);
			$o2 =& new $pc;
			if ($pc != 'persistentobject' && $pc != 'descriptedobject' && $pc != 'pwbobject' && $pc != ''){
				$tns[] = 'LEFT OUTER JOIN `'.$o1->tableName().'` ON `'. $o2->tableName().'`.id = `'.$o1->tableName().'`.super';
			}
		}
		$tn = implode(' ',$tns);
		return $tn;
	}

	function idRelations(){
		return $this->tableName().'.id=' . $this->getID();
	}

	function is(&$other){
		return parent::is($other) || (get_class($other)==get_class($this) && $other->id->getValue() == $this->id->getValue());
	}

	function idRestrictions(){
		$rcs = get_related_classes(getClass($this));
		$rcs [] = getClass($this);
		$rss []='1=1';
		foreach($rcs as $rc){
			$sup = get_parent_class($rc);
			if ($sup != 'persistentobject' && $sup != 'descriptedobject' && $sup != 'pwbobject' && $sup != ''){
				$o1 = new $rc;
				$o2 = new $sup;
				$rss[] = '('.$o2->tableName().'.id = '.$o1->tableName().'.super'.
					' or '. $o1->tableName().'.super IS NULL)';
			}
		}
		return implode(' AND ', $rss);
	}

	function &basicLoad() {
		$sql = $this->loadSQL();
		$db =& DB::Instance();
		$rec = $db->SQLExec($sql, FALSE, $this);
		if (!$rec) return false;
		$record = $db->fetchRecord($rec);
		return $record;
	}

	function loadSQL(){
		return 'SELECT ' . $this->fieldNames('SELECT') . ' FROM ' . $this->tableNames() . ' WHERE '.$this->idRelations(). ';';
	}

	function basicInsert() {
		$values = '';
		$this->PWBversion->setValue(0);
		$this->PWBversion->commitChanges();
		foreach ($this->allFieldsThisLevel() as $index => $field) {
			$values .= $field->insertValue();
		}
		$values = substr($values, 0, -2);
		$sql = 'INSERT IGNORE INTO `' . $this->tableName() . '` (' . $this->fieldNames('INSERT') . ') VALUES ('.$values.')';
		$db =& DB::Instance();
		$db->SQLExec($sql, TRUE, & $this, & $rows);
		$ok = $rows > 0;
		$this->existsObject = $ok;
		return $ok;
	}

	function updateString() {
		$values = '';
		$ver = $this->PWBversion->getValue();
		$this->PWBversion->setValue($this->PWBversion->getValue()+1);
		foreach ($this->allFieldsThisLevel() as $index => $field) {
			$values .= $field->updateString();
		}
		$values = substr($values, 0, -2);
		return "UPDATE `" . $this->tableName() . "` SET $values WHERE id=" . $this->getID() . " AND PWBversion=".$ver;
	}

	function basicUpdate() {
		$sql = $this->updateString();
		$db =& DB::Instance();
		$ok = $db->SQLExec($sql, FALSE, $this, &$rows);
		if ($ok !== FALSE && $rows>0){
			return true;
		} else{
			$this->PWBversion->flushChanges();
			return false;
		}
	}

	function basicDelete() {
		if (!$this->existsObject) return true;
		$sql = 'DELETE FROM `' . $this->tableName() . '` WHERE id=' . $this->getId();
		$db =& DB::Instance();
		$can = TRUE;
		foreach ($this->allFieldsThisLevel() as $f) {
			$can = $can & $f->canDelete();
		}
		if ($can){
			$db->SQLExec($sql, FALSE, $this);
			$this->existsObject=FALSE;
		} else {
			trace('The object is not erasable<BR>\n');
		}
		return $can;
	}

	function & visit(& $obj) {
		return $obj->visitedPersistentObject($this);
	}

	function indexValues() {
		$ret = "";
		$idFields = $this->findIndexField();
		foreach ($idFields as $index => $field) {
			$ret .= $field->viewValue() . ", ";
		}
		$ret = substr($ret, 0, -2);
		return $ret;
	}

	function & getWithIdChildren($id) {
		$class = getClass($this);
		$subs = get_subclasses($class);
		$cant = 0;
		foreach ($subs as $sub) {
			$col = & new PersistentCollection($sub);
			$col->conditions["super"] = array (
				"=",
				$id
			);
			$objs = & $col->objects();
			$cant = count($objs);
			if ($cant > 0)
				break;
		}
		if ($cant == 0)
			return $this;
		else {
			$sub = & $objs[0];
			$sub->setParent($this);
			$sub2 = & $sub->getWithIdChildren($id);
			return $sub2;
		}
	}
	function & createInstance() {
		if ($this->isNotTopClass($this)) {
			$this->setParent($this->create(get_parent_class(getClass($this))));
		}
		$this->basicInitialize();
		return $this;
	}
	/**
	 * Setter for the parent of the object.
	 * @access private
	 */
	function setParent(& $obj) {
		$this->parent = & $obj;
		$arr = $this->parent->allFieldNames();
		foreach ($arr as $name) {
			if ($name != 'id' && $name != 'super')
				$this-> $name = & $this->parent-> $name;
		}
	}
	function & getParent() {
		return $this->parent;
	}
	function & getWithIdParent() {
		$class = getClass($this);
		if ($this->isNotTopClass($class)) {
			/* We have to load all the way up */
			$parclass = get_parent_class($class);
			$par = & new $parclass;
			$parid = $this->super->getValue();
			trace("Loading parent from " . $parid . " for ");
			$par->loadFromId($parid);
			$p = & $par->getWithIdParent();
			$this->setParent($p);
			return $this;
		} else {
			return $this;
		}
	}
	function & allSQLFields() {
		return $this->fieldsWithSQLNames($this->allFieldNames());
	}
	function & fieldsWithSQLNames($names) {
		$arr = array ();
		foreach ($names as $name) {
			$f =& $this->fieldNamed($name);
			$arr[$f->sqlName()] = & $f;
		}
		return $arr;
	}
	/**
	 * @category Persistence
	 */
	/**
	 * Function for loading an object (class method)
	 */
	function & getWithId($class, $id) {
		$obj = & new $class;
		$obj = & $obj->loadFromId($id);
		return $obj;
	}

	function &reloaded() {
		$class = getClass($this);
		$obj =& new $class;
		if ($this->existsObject) {
			$obj->setID($this->getID());
			$obj->loadFromId($this->getID());
		}
		return $obj;
	}

	function & getWithIndex($class, $indArray) {
		$cs = & new PersistentCollection($class);
		foreach($indArray as $i=>$v){
			$cs->conditions[$i]=array('=',$v);
		}
		return $cs->first();
	}
	/**
	 * Loads an object from an id, not worrying about inheritance (class)
	 */
	function & loadFromId($id) {
		$this->setID($id);
		$rec =& $this->basicLoad();
		if (!$rec) return $f = false;
		return $this->loadFromRec(&$rec);
	}

	function &loadFromRec(&$rec){
		$obj =& $this->chooseSubclass($rec);
		$obj->loadFrom($rec);
		return $obj;
	}
	function &chooseSubclass(&$rec){
		$c = getClass($this);
		$rcs = get_subclasses($c);
		foreach($rcs as $rc){
			$o =& new $rc;
			if ($o->canBeLoaded($rec)){
				return $o;
			}
		}
		return new $c;
	}
	function canBeLoaded(&$rec){
		return isset($rec[$this->id->sqlName()]);
	}
	function prepareToSave(){
		$fs =& $this->allFields();
		foreach(array_keys($fs) as $k){
			$fs[$k]->prepareToSave();
		}
	}
	function save() {
		if ($this->modified){
			$this->prepareToSave();
			$this->commitChanges();
			if ($this->existsObject) {
				return $this->update();
			}
			else {
				return $this->insert();
			}
		} else {
			return true;
		}
	}
	function update() {
		if ($this->isNotTopClass($this)) {
			$p = & $this->getParent();
			$p->id->setValue($this->super->getValue());
			$ok = $p->update();
		} else $ok=true;
		if ($ok){
			$ok = $this->basicUpdate();
		} else {
			return false;
		}
		return $ok;
	}
	function insert() {
		if ($this->isNotTopClass($this)) {
			$p = & $this->getParent();
			$ok = $p->insert();
			$this->super->setValue($p->id->getValue());
		} else $ok=true;
		if ($ok){
			$ok = $this->basicInsert();
		} else {
			return false;
		}
		if (!$ok && $this->isNotTopClass($this)){
			$p->delete();
		}
		return $ok;
	}
	function delete() {
		if ($this->isNotTopClass($this)) {
			$p = & $this->getParent();
			$p->delete();
		}
		return $this->basicDelete(); /*The one from before */
	}
}
?>