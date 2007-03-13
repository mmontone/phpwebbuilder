<?

class PersistentObject extends DescriptedObject {
	/**
	 * Class's table
	 */
	var $table;
	/**
	 * If the object is new, or has been persisted
	 */
	var $existsObject;
	var $idN;
	/**
	 * Sets the objec's ID, and notifies all the fields
	 * (only the ones of this level)
	 */
	function setID($id) {
		foreach ($this->allFieldNamesThisLevel() as $field) {
			$this->$field->setID($id);
		}
		$this->idN = $id;
		$this->registerGlobalObject();
	}
	function PersistentObject($parems=array(),$create=true, $createMetaData=false){
		$this->createMetaData = $createMetaData;
		parent::PWBObject($parems);
		if ($create) {
			$this->initializeObject();
		}
	}
	function &getMetaData($class){
		$metadata =& getSessionGlobal('persistentObjectsMetaData');
		$class = strtolower($class);
		if (!isset($metadata[$class])){
			$metadata[$class] =& new $class(array(), false, true);
			if ($metadata[$class]->isNotTopClass($metadata[$class])) {
				$metadata[$class]->setParent(PersistentObject::getMetaData(get_parent_class($class)));
			}
			$metadata[$class]->basicInitialize();
			foreach($metadata[$class]->fieldNames as $name){
				unset($metadata[$class]->$name->collection);
			}
		}
		return $metadata[$class];
	}
	function __wakeup(){
		parent::__wakeup();
		$this->registerGlobalObject();
	}
	function &findGlobalObject($class, $id){
		global $persistentObjects;
		$o =& $persistentObjects[strtolower($class)][$id];
		if ($o===null) {
			//echo "no encontrado $class id: $id";
			return $o;
		} else {
			//echo "recuperando $class id: $id";
			return $o->getRealChild();
		}
	}
	function registerGlobalObject(){
		global $persistentObjects;

        $id = $this->getId();
		if ($id!=0) {
			$persistentObjects[getClass($this)][$id] =& $this;
        }
	}
	function &getRealChild(){
		if (!isset($this->child)) {
			return $this;
		} else {
			return $this->child->getRealChild();
		}

	}
	/**
	 * Gets the ID of the object (Of the concrete class)
	 */
	function getID() {
		return $this->idN;
	}
	/**
	 * Gets the ID of the object, of the specified class. If the object doesn't
	 * have that class, it prints an error message.
	 */
    function getIdOfClass($class){
    	if (strcasecmp(getClass($this), $class)==0) {
    		return $this->getId();
    	} else {
    		#@check $this->parent!==null@#
    		return $this->parent->getIdOfClass($class);
    	}
    }
	function existsObject() {
		return $this->existsObject;
	}
	/**
	 * Gets all the fields of all the levels for the SQL query
	 */
	function &allFieldsAllLevels(){
		$rcs = get_related_classes(getClass($this));
		$fs = array();
		foreach($rcs as $rc){
			if ($rc != 'persistentobject' && $rc != 'descriptedobject' && $rc != 'pwbobject') {
				$o = PersistentObject::getMetaData($rc);
				$fs = array_merge($fs, $o->allSQLFields());
			}
		}
		$fs = array_merge($fs, $this->allSQLFields());
		return $fs;
	}
	/**
	 * Returns the table name for the object.
	 */
	function tableName() {
		return $this->tableNamePrefixed('');
	}

    function tableNamePrefixed($prefix) {
    	return '`' . $this->getTablePrefixed($prefix) . '`';
    }
	function getTable() {
		return $this->getTablePrefixed('target_');
	}

    function getTablePrefixed($prefix) {
        return constant('baseprefix') . $prefix . $this->table;
    }

	/**
	 * Returns the table names for the object (including all
	 * hierarchy that involves it)
	 */
	function getTables() {
		return $this->getTablesPrefixed('target_');
	}

    function getTablesPrefixed($prefix) {
        if (!isset($this->allObjectTables)){
	        $tns[] = $this->tableName() . ' AS ' . $this->tableNamePrefixed('{$prefix}');
	        $p0 = getClass($this);
	        $pcs = get_superclasses($p0);
	        $o0 =& $this;
	        foreach($pcs as $pc){
	            if ($pc != 'persistentobject' && $pc != 'descriptedobject' && $pc != 'pwbobject' && $pc != ''){
	            	$o1 =& PersistentObject::getMetaData($pc);
	                $tns[] = 'LEFT OUTER JOIN '.$o1->tableName().' AS ' . $o1->tableNamePrefixed('{$prefix}') . ' ON '. $o1->tableNamePrefixed('{$prefix}').'.id = '.$o0->tableNamePrefixed('{$prefix}').'.super';
	            } else {
	            	break;
	            }
	            $o0 =& $o1;
	            $p0 = $pc;
	        }
	        $scs = get_subclasses(getClass($this));
	        foreach($scs as $sc){
	            $o1 =& PersistentObject::getMetaData($sc);
	            $pc = get_parent_class($sc);
	            $o2 =& PersistentObject::getMetaData($pc);
	            if ($pc != 'persistentobject' && $pc != 'descriptedobject' && $pc != 'pwbobject' && $pc != ''){
	                $tns[] = 'LEFT OUTER JOIN '.$o1->tableName(). ' AS ' . $o1->tableNamePrefixed('{$prefix}') . ' ON '. $o2->tableNamePrefixed('{$prefix}').'.id = '. $o1->tableNamePrefixed('{$prefix}').'.super';
	            } else {
	            	break;
	            }
	        }
	        $this->allObjectTables = implode(' ',$tns);
        }
        $tableNames = '';
        $tableNames = eval('return "'.$this->allObjectTables.'";');
        return array($tableNames);
    }


	function tableNames(){
		$tns = $this->getTables();
		return $tns[0];
	}
	function &basicInsert() {
		$values = '';
		$this->PWBversion->setValue(0);
		$this->PWBversion->primitiveCommitChanges();
		foreach ($this->allFieldsThisLevel() as $index => $field) {
			$values .= $field->insertValue();
		}
		$values = substr($values, 0, -2);
		$sql = 'INSERT INTO ' . $this->tableName() . ' (' . $this->fieldNames('INSERT') . ') VALUES ('.$values.')';
		$db =& DBSession::Instance();
		$rows=0;
		$res =& $db->SQLExec($sql, TRUE, $this, $rows);
		if (is_exception($res)) {
			return $res;
		}
		if ($rows!=1) {
			$ex =& new PWBException(array('message' => 'Could not insert'));
			return $ex->raise();
		}
		$this->existsObject = true;
		return $res;
	}
	/**
	 * Returns the string for updating the object
	 */
	function updateString() {
		$values = '';
		$ver = $this->PWBversion->getValue();
		$this->PWBversion->setValue($this->PWBversion->getValue()+1);
		foreach ($this->allFieldsThisLevel() as $index => $field) {
			$values .= $field->updateString();
		}
		$values = substr($values, 0, -2);
		return "UPDATE " . $this->tableName() . " SET $values WHERE id=" . $this->getID() . " AND PWBversion=".$ver;
	}
	/**
	 * Updates this level of the object, taking into account versioning
	 */
	function &basicUpdate() {
		$sql = $this->updateString();
		$db =& DBSession::Instance();
		$rows=0;
		$res = $db->SQLExec($sql, FALSE, $this, $rows);
		if (is_exception($res)) {
			return $res;
		}
		else {
			if ($rows == 0) {
				$db =& DBSession::Instance();
				$rec =& $db->query('SELECT PWBversion FROM ' . $this->tableName() . ' WHERE id=' . $this->getId());
				if ($rec['PWBversion'] !== $this->PWBversion->getValue()) {
					$ex =& new PWBException(array('message' => 'Versioning error'));
				}
				else {
					$ex =& new PWBException(array('message' => 'Could not update'));
				}

				return $ex;
			} else {
				$error = false;
				return $error;
			}
		}
	}
	/**
	 * Deletes this level of the object. Fails if a collection is not empty
	 */
	function canDelete(){
		$can = TRUE;
		foreach ($this->allFieldsThisLevel() as $f) {
			$can = $can && $f->canDelete();
		}
		return $can;
	}
	function &basicDelete() {
		if (!$this->existsObject) return true;
		$sql = 'DELETE FROM ' . $this->tableName() . ' WHERE id=' . $this->getId();
		$db =& DBSession::Instance();
		$res =& $db->SQLExec($sql, FALSE, $this, $rows=0);
		if (!is_exception($res)) {
			$this->existsObject=FALSE;
		}
		return $res;
	}
	/**
	 * Returns a string representation of the object
	 */
	function indexValues() {
		$ret = "";
		$idFields = $this->allIndexFields();
		foreach ($idFields as $index => $field) {
			$ret .= $field->viewValue() . ", ";
		}
		$ret = substr($ret, 0, -2);
		return $ret;
	}
	/**
	 * Perform post-creation tasks for the object (initialization and inheritance)
	 */
	function & createInstance() {
		if ($this->createMetaData) return $this;
		if ($this->isNotTopClass($this)) {
			$c = get_parent_class(getClass($this));
			$this->setParent(new $c(array(),false));
		}
		$this->basicInitialize();
		return $this;
	}
	function initializeObject(){
		//$this->attachFieldsEvents();
		//print_backtrace(getClass($this));
	}
	/**
	 * Setter for the parent of the object.
	 * @access private
	 */
	function setParent(& $obj) {
		$this->parent = & $obj;
		$obj->child =& $this;
		$arr = $this->parent->allFieldNames();
		foreach ($arr as $name) {
			if ($name != 'id' && $name != 'super')
				$this-> $name = & $this->parent-> $name;
		}
	}
	/**
	 * Returns the parent of the object
	 */
	function & getParent() {
		return $this->parent;
	}
	/**
	 * Returns an array of the sql names of the fields (all levels)
	 */
	function & allSQLFields() {
		return $this->fieldsWithSQLNames($this->allFieldNames());
	}
	/**
	 * Returns an array of the sql names of the specified fields
	 */
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
		if ($id==0) {$n=null;return$n;}
		$o =&PersistentObject::findGlobalObject($class, $id);
		if ($o!==null) return $o;
		return PersistentObject::getWithIndex($class,array('id'=>$id));
	}
	/**
	 * Reloads the object from the database
	 */
	function &reload() {
		PersistentObject::getWithIndex(getClass($this),array('id'=>$this->getId()));
		return $this;
	}
	/**
	 * Gets the object from the database, using the specified index values
	 * (field=>value)
	 */
	function & getWithIndex($class, $indArray) {
		$cs = & new PersistentCollection($class);
		$cs->limit = 1;
		foreach($indArray as $i=>$v){
			$cs->setCondition($i,'=',$v);
		}
		return $cs->first();
	}
	/**
	 * Loads an object from a database record
	 */
	function &loadFromRec(&$rec){
		$obj =& $this->chooseSubclass($rec);
		$obj->loadFrom($rec);
		$obj->attachFieldsEvents();
		$obj->initializeObject();
		return $obj;
	}
	/**
	 * Chooses the right subclass for the object
	 */
	function &chooseSubclass(&$rec){
		$c = getClass($this);
		$rcss = get_subclasses($c);
		$rcs = array_reverse($rcss);
		foreach($rcs as $rc){
			$o =& PersistentObject::getMetaData($rc);
			if ($o->canBeLoaded($rec)){
				return new $rc(array(), false);
			}
		}
		$o =& new $c(array(), false);
		return $o;
	}
	/**
	 * Checks if the subclass can be loaded from the record
	 */
	function canBeLoaded(&$rec){
		return isset($rec[$this->id->sqlName()]);
	}
	/**
	 * Prepares the object to be saved (for cascading and inheritance-by-relation)
	 */
	function prepareToSave(){
		$fs =& $this->allFields();
		foreach(array_keys($fs) as $k){
			$fs[$k]->prepareToSave();
		}
	}
	/**
	 * Persists the object in the database. Returns if everything worked
	 */
	function &save() {
		if ($this->existsObject) {
			$res =& $this->update();
		}
		else {
			$res =& $this->insert();
			$this->triggerEvent('id_changed',$n=null);
		}

		return $res;
	}
	/**
	 * Updates the object in the database
	 */
	function &update() {
		$res = null;
		if ($this->isNotTopClass($this)) {
			$p = & $this->getParent();
			$p->id->setValue($this->super->getValue());
			$res =& $p->update();
		}
		if (!is_exception($res)){
			$res =& $this->basicUpdate();
			$this->markAsUpdated();
		}

		return $res;
	}
	function markAsUpdated(){
		DBUpdater::markUpdated(getClass($this));
	}
	function flushUpdate() {
		if ($this->isNotTopClass($this)) {
			$p = & $this->getParent();
			$p->flushUpdate();
		}
		$this->PWBversion->primitiveFlushChanges();
		//echo 'PWBVersion flushed value: ' . getClass($this) . ' : ' . $this->PWBversion->getValue();
	}
	/**
	 * Inserts the object in the database
	 */
	function &insert() {
		$res = null;
		if ($this->isNotTopClass($this)) {
			$p = & $this->getParent();
			$res =& $p->insert();
			$this->super->setValue($p->id->getValue());
		}
		if (!is_exception($res)){
			$res =& $this->basicInsert();
			$this->markAsUpdated();
			/*
			if (!is_exception($res) && $this->isNotTopClass($this)){
				$res =& $p->delete();
			}
			*/
		}

		return $res;
	}

	function flushInsert() {
		if ($this->isNotTopClass($this)) {
			$p = & $this->getParent();
			$p->flushInsert();
			$this->super->primitiveFlushChanges();
		}
		$this->id->primitiveFlushChanges();
		$this->existsObject = false;
	}

	function commitMetaFields() {
		if ($this->isNotTopClass($this)) {
			$p = & $this->getParent();
			$p->commitMetaFields();
			$this->super->primitiveCommitChanges();
		}
		$this->id->primitiveCommitChanges();
		$this->PWBversion->primitiveCommitChanges();
	}

	/**
	 * Deletes the object from the database
	 */
	function &delete() {
		$res = null;
		if ($this->canDelete())  {
			if ($this->isNotTopClass($this)) {
				$p = & $this->getParent();
				$res =& $p->delete();
			}
			if (!is_exception($res))  {
				$res =& $this->basicDelete();
			}
		}
		else {
			$res =& new PWBException(array('message' => 'Cannot delete object'));
		}
		return $res;
	}
		/**
	 * finds all similar objects (objects with same atributes set in same values)
	 * Returns a PersistentCollection
	 */
	function &findMatches() {
		$col =& new PersistentCollection(getClass($this));
		foreach($this->fieldNames() as $f) {
			$field =& $this->fieldNamed($f);
			if (!$field->isEmpty()) {
				$col->setCondition($f, '=', $field->getValue());
			}
		}
		return $col;
	}

	function tableForField($field) {
		return $this->tableForFieldPrefixed($field, '');
	}

    function tableForFieldPrefixed($field, $prefix) {
		$o1 =& $this;
		//echo 'Checking class ' . getClass($o1). ' field ' . $field . '<br />';
		if (in_array($field, $o1->allFieldNamesThisLevel())) {
			//echo 'Found ' . getClass($o1) . '.' . $field. '<br />';
			return $o1->getTablePrefixed($prefix);
		}

		$p0 = getClass($this);
		$pcs = get_superclasses($p0);
		foreach($pcs as $pc){
			$o1 =& PersistentObject::getMetaData($pc);
			//echo 'Checking class ' . getClass($o1). ' for field ' . $field . '<br />';
			if (getClass($o1) == 'pwbobject') {
				print_backtrace_and_exit('Field not found: ' . $field . ' in ' . $this->printString());
			}

            if (in_array($field, $o1->allFieldNamesThisLevel())) {
				//echo 'Found ' . getClass($o1) . '.' . $field. '<br />';
				return $o1->getTablePrefixed($prefix);
			}
		}
	}

	// GARBAGE COLLECTION
	var $color='black';
	var $buffered=false;
	function addGCFields(){
		if (defined('garbage_collection')){
			$this->addField(new VersionField(array('fieldName'=>"refCount",'default'=>'0')));	$this->refCount->setValue('0');
			$this->addField(new BoolField(array('fieldName'=>"rootObject",'default'=>true)));	$this->rootObject->setValue(false);
		}
	}
	function makeRootObject(){
		$this->rootObject->setValue(true);
	}
	function deleteRootObject(){
		$this->rootObject->setValue(false);
		$this->posibleGarbageRoot();
	}
	function incrementRefCount(){
		if (!defined('garbage_collection')) return;
		$this->refCount->increment();
	}
	function decrementRefCount(){
		if (!defined('garbage_collection')) return;
		$this->refCount->decrement();
		if ($this->refCount->getValue()==0){
			$this->release();
		} else {
			$this->posibleGarbageRoot();
		}
	}
	function release(){
		foreach($this->allFieldNames() as $f){
			$this->$f->mapChild('decrementRefCount');
		}
		$this->color='black';
		if (!$this->buffered){
			$this->delete();
		}
	}
	function posibleGarbageRoot(){
		$this->color='purple';
		$this->buffered=true;
		$GLOBALS['bufferedRoots'][$this->getInstanceId()]=&$this;
	}
	function CollectCycles(){
		PersistentObject::MarkRoots();
		PersistentObject::ScanRoots();
		PersistentObject::CollectRoots();
	}
	function MarkRoots(){
		foreach(array_keys($GLOBALS['bufferedRoots']) as $k){
			$n =& $GLOBALS['bufferedRoots'][$k];
			if ($n->color=='purple'){
				$n->markGray();
			} else {
				$n->buffered=false;
				unset($GLOBALS['bufferedRoots'][$k]);
				if ($n->color=='black' and $n->refCount->getValue()==0){
					$n->delete();
				}
			}
		}
	}
	function ScanRoots(){
		foreach(array_keys($GLOBALS['bufferedRoots']) as $k){
			$GLOBALS['bufferedRoots'][$k]->scan();
		}
	}
	function CollectRoots(){
		foreach(array_keys($GLOBALS['bufferedRoots']) as $k){
			$n =& $GLOBALS['bufferedRoots'][$k];
			$n->buffered=false;
			unset($GLOBALS['bufferedRoots'][$k]);
			$n->collectWhite();
		}
	}
	function markGray(){
		if ($this->rootObject->getValue())return;
		if ($this->color!='gray'){
			$this->color='gray';
			foreach($this->allFieldNames() as $f){
				$this->$f->mapChild(
					#@lam &$t->	$t->refCount->decrement();$t->markGray();@#
	    		);
			}
		}
	}
	function scan(){
		if ($this->color=='gray'){
			if ($this->refCount->getValue()>0){
				$this->scanBlack();
			} else {
				$this->color='white';
				foreach($this->allFieldNames() as $f){
					$this->$f->mapChild('scan');
				}
			}
		}
	}
	function scanBlack(){
		$this->color='black';
		foreach($this->allFieldNames() as $f){
			$this->$f->mapChild(
				#@lam &$t->	$t->refCount->increment();if ($t->color!='black'){$t->scanBlack();}@#
    		);
		}
	}
	function collectWhite(){
		if ($this->color=='white' and !$this->buffered){
			$this->color='black';
			foreach($this->allFieldNames() as $f){
				$this->$f->mapChild('collectWhite');
			}
			$this->delete();
		};
	}
	function ResetRefCounts(){
		$DB=& DBSession::beginRegisteringAndTransaction();
		echo 'setting 0';
		foreach(get_subclasses('PersistentObject') as $sc){
			$obs =& new PersistentCollection($sc);
			$obs->collect(lambda('&$elem', '$elem->refCount->setValue(0);'));
		}
		echo 'setting ref';
		foreach(get_subclasses('PersistentObject') as $sc){
			$obs =& new PersistentCollection($sc);
			$obs->collect(lambda('&$elem', '$elem->mapChild("incrementRefCount");'));
		}
		$DB->commit();
	}

}
?>