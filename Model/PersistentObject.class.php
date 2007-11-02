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


	function prepareToSave() {

	}

	function setID($id) {
		foreach ($this->metadata->allFieldNamesThisLevel() as $field) {
			$this->$field->setID($id);
		}
		//$this->idN = $id;
		$this->registerGlobalObject();
	}
	function PersistentObject($parems=array(),$create=true, $createMetaData=false){
		$this->createMetaData = $createMetaData;
		parent::PWBObject($parems);
		if ($create) {
			$this->attachFieldsEvents();
            $this->initializeObject();
		}
	}
	function &getMetaData($class){
		return PersistentObjectMetaData::getMetaData($class);
	}
		/**
	 * Returns the fields with specified names
	 */
	function & fieldsWithNames($names) {
		$arr = array ();
		foreach ($names as $name) {
			$f =& $this->fieldNamed($name);
			#@gencheck if ($f==null)
			{
				print_backtrace('Object '.getClass($this).' doesn\'t have field '.
					$name.' in '.print_r($names, TRUE));
			}
			//@#
			$arr[$name] = & $f;
		}
		return $arr;
	}

	function __wakeup(){
		parent::__wakeup();
		$this->registerGlobalObject();
	}
	function &findGlobalObject($class, $id){
		global $persistentObjects;
		return $persistentObjects[strtolower($class)][$id];
	}
	function registerGlobalObject(){
		global $persistentObjects;
		foreach($this->getPersistentClasses() as $class){
	        $id = $this->getIdOfClass($class);
			if ($id!=0) {
				$persistentObjects[$class][$id] =& $this;
	        }
		}
	}

    function unregisterGlobalObject() {
    	global $persistentObjects;
		foreach($this->getPersistentClasses() as $class){
	        $id = $this->getIdOfClass($class);
			if ($id!=0) {
				unset($persistentObjects[$class][$id]);
	        }
		}
    }
	/**
	 * Gets the ID of the object (Of the concrete class)
	 */
	function getID() {
		return $this->id->getValue();
	}
	/**
	 * Gets the ID of the object, of the specified class. If the object doesn't
	 * have that class, it prints an error message.
	 */
    function getIdOfClass($class){
    	$id =& $this->fields[strtolower($class)]['id'];
    	if($id!==null){
    		return $id->getValue();
    	} else {
    		return 0;
    	}
    }
	function existsObject() {
		return $this->existsObject;
	}
	function &basicInsert($class) {
		$values = '';
		$this->fields[$class]['PWBversion']->setValue(0);
		$this->fields[$class]['PWBversion']->primitiveCommitChanges();
		$md =& PersistentObjectMetaData::getMetaData($class);
		foreach ($this->fields[$class] as $index => $field) {
			$values .= $field->insertValue();
		}
		$values = substr($values, 0, -2);
		$sql = 'INSERT INTO ' . $md->tableName() . ' (' . $md->fieldNames('INSERT') . ') VALUES ('.$values.')';
		$db =& DBSession::Instance();
		$rows=0;
		$res =& $db->query ($sql);
        if (!is_exception($res)) {
        	$nid = $db->getLastId();
	        foreach (array_keys($this->fields[$class]) as $index) {
				$this->fields[$class][$index]->setID($nid);
			}
	        $rows = $db->getRowsAffected($res);
        }
		if (is_exception($res)) {
			return $res;
		}
		if ($rows!=1) {
			$ex =& new PWBException(array('message' => 'Could not insert'));
			return $ex->raise();
		}
		$this->existsObject = true;
		#@sql_dml_echo echo ("The ID is: {$this->getID()}, {$this->debugPrintString()}");@#
		return $res;
	}
	/**
	 * Returns the string for updating the object
	 */
	function updateString($class) {
		$values = '';
		$ver = $this->fields[$class]['PWBversion']->getValue();
		#@sql_dml_echo echo ("Current V: $ver, stored version: {$this->fields[$class]['PWBversion']->getStoredValue()}, {$this->debugPrintString()}");@#
		$this->fields[$class]['PWBversion']->setValue($ver+1);
		$md =& PersistentObjectMetaData::getMetaData($class);
		foreach ($this->fields[$class] as $index => $field) {
			$values .= $field->updateString();
		}
		$values = substr($values, 0, -2);
		return "UPDATE " . $md->tableName() . " SET $values WHERE id=" . $this->getIdOfClass($class) . " AND PWBversion=".$ver;
	}
	/**
	 * Updates this level of the object, taking into account versioning
	 */
	function &basicUpdate($class) {
		$sql = $this->updateString($class);
		$db =& DBSession::Instance();
		$rows=0;
		$res = $db->query($sql);
		if (is_exception($res)) {
			return $res;
		}
		else {
			if ($db->getRowsAffected($res) == 0) {
				$db =& DBSession::Instance();
				$md =& PersistentObjectMetaData::getMetaData($class);
				$rec =& $db->query('SELECT PWBversion FROM ' . $md->tableName() . ' WHERE id=' . $this->getIdOfClass($class));
				if ($rec['PWBversion'] !== $this->fields[$class]['PWBversion']->getStoredValue()) {
					$ex =& new DBError(array('message' => 'Versioning error, '. $this->printString().' has version '.$rec['PWBversion'] .' and '.  $this->fields[$class]['PWBversion']->getStoredValue().' was expected'));
				}
				else {
					$ex =& new DBError(array('message' => 'Could not update'));
				}

				return $ex->raise();
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
		foreach ($this->fieldsWithNames($this->metadata->allFieldNames()) as $f) {
			$can = $can && $f->canDelete();
		}
		return $can;
	}
	function &basicDelete($class) {
		if (!$this->existsObject) return true;
		$md =& PersistentObjectMetaData::getMetaData($class);
		$sql = 'DELETE FROM ' . $md->tableName() . ' WHERE id=' . $this->getIdOfClass($class);
		$db =& DBSession::Instance();
		$res =& $db->query($sql);
		return $res;
	}
	/**
	 * Returns a string representation of the object
	 */
	function indexValues() {
		$ret = "";
		$idFields = $this->fieldsWithNames($this->metadata->allIndexFieldNames());
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
		$this->metadata =& PersistentObjectMetaData::getMetaData(getClass($this));
		$this->basicInitialize();
		return $this;
	}

    function initializeObject() {
        // Redefine in subclasses to initialize a newly created (not loaded) object
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
		foreach($this->getPersistentClasses() as $sc){
			PersistentCollection::changedClass($sc);
		}
		return $res;
	}

    /*
     * Raises a database error
     */
    function &raiseDBError(&$error) {
    	return $error->primRaise();
    }
	/**
	 * Updates the object in the database
	 */
	function &update() {
		$res = null;
		foreach($this->getPersistentClasses() as $sc){
			$res =& $this->basicUpdate($sc);
			if (is_exception($res)){return $res;}
		}
		$this->markAsUpdated();
		return $res;
	}
	function markAsUpdated(){
		DBUpdater::markUpdated(getClass($this));
	}
	function flushUpdate() {
		foreach($this->getPersistentClasses() as $sc){
			$this->fields[$sc]['PWBversion']->primitiveFlushChanges();
		}
		#@sql_dml_echo echo ("Flushing to V: {$this->fields[getClass($this)]['PWBversion']->getStoredValue()}, {$this->debugPrintString()}");@#
	}
	/**
	 * Inserts the object in the database
	 */
	function &insert() {
		$res = null;
		foreach($this->getPersistentClasses() as $sc){
			if (DescriptedObject::isNotTopClass($sc)) {
				$this->fields[$sc]['super']->setValue($this->fields[strtolower(get_parent_class($sc))]['id']->getValue());
			}
			$res =& $this->basicInsert($sc);
			if (is_exception($res)){break;}
		}
		if (!is_exception($res)){
			$this->markAsUpdated();
		}
		return $res;
	}

	function flushInsert() {
		$this->existsObject = false;
	}

	function commitMetaFields() {
		foreach($this->getPersistentClasses() as $sc){
			if (DescriptedObject::isNotTopClass($sc)) {
				$this->fields[$sc]['super']->primitiveCommitChanges();
			}
			$this->fields[$sc]['id']->primitiveCommitChanges();
			$this->fields[$sc]['PWBversion']->primitiveCommitChanges();
		}
	}

	/**
	 * Deletes the object from the database
	 */
	function &delete() {
		$res = null;
		$ex =& $this->verifyDeletion();
        if (!is_exception($ex)) {
			foreach($this->getPersistentClasses() as $sc){
				$res =& $this->basicDelete($sc);
	            if (is_exception($res))  {return $res;}
			}
			$this->existsObject=FALSE;
			foreach($this->getPersistentClasses() as $sc){
				PersistentCollection::changedClass($sc);
			}
	    	return $res;
		}
		return $ex;
	}

    function verifyDeletion() {
    	if (!$this->canDelete())  {
            $ex =& new DBError(array('message' => 'Cannot delete object'));
            return $ex->raise();
        }
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
	// GARBAGE COLLECTION
	var $color='black';
	var $buffered=false;
	function addGCFields(){
		if (defined('garbage_collection')){
			$this->addField(new NumField(array('fieldName'=>"refCount",'default'=>'0')));	$this->refCount->setValue('0');
			$this->addField(new BoolField(array('fieldName'=>"rootObject",'default'=>true)));	$this->rootObject->setValue(false);
		} else {
			$this->refCount =& new ValueHolder(0);
			$this->rootObject =& new ValueHolder(true);
		}
	}
	function removedAsTarget(&$elem, $field){
		foreach($this->metadata->allFieldNames() as $f){
			$this->$f->removedAsTarget($elem, $field);
		}
	}
	function addedAsTarget(&$elem, $field){
		foreach($this->metadata->allFieldNames() as $f){
			$this->$f->addedAsTarget($elem, $field);
		}
	}

	function makeRootObject(){
	  $this->rootObject->setValue(true);
		$this->registerPersistence();
	}
	function deleteRootObject(){
		$this->rootObject->setValue(false);
		$this->posibleGarbageRoot();
	}
	function &incrementRefCount(){
		if (!defined('garbage_collection')) return $this;
		$this->refCount->increment();
		return $this;
	}
	function decrementRefCount(){
		if (!defined('garbage_collection')) return $this;
		$this->refCount->decrement();
		$this->posibleGarbageRoot();
	}
	function release(){
		$this->mapChild(
			#@lam &$e->$e->decrementRefCount();return $e;@#
		);
		$this->color='black';
		if (!$this->buffered){
			#@persistence_echo echo 'deleting garbage:'.$this->printString().'<br/>';@#
			$this->delete();
		}
	}
	function posibleGarbageRoot(){
		$this->buffered=true;
		$gs =& getSessionGlobal('bufferedRoots');
		$gs[$this->getInstanceId()]=&$this;
		#@persistence_echo echo 'possible garbage: '.$this->printString().'<br/>';@#
	}
	function CollectCycles(){
		PersistentObject::MarkRoots();
		PersistentObject::ScanRoots();
		PersistentObject::CollectRoots();
	}
	function isErasable(){
		return $this->color=='black' and $this->refCount->getValue()==0 && !$this->rootObject->getValue();
	}
	function MarkRoots(){
		$gs =& getSessionGlobal('bufferedRoots');
		if ($gs==null) $gs=array();
		#@persistence_echo echo 'finding garbage<br/>';@#
		foreach(array_keys($gs) as $k){
			$n =& $gs[$k];
			if ($n->isErasable()){
				$n->buffered=false;
				unset($gs[$k]);
				$n->release();
			} else {
				$n->color='purple';
			}
		}
		foreach(array_keys($gs) as $k){
			$n =& $gs[$k];
			#@persistence_echo echo 'possible garbage root: '.$n->printString().'<br/>';@#
			if ($n->color=='purple'){
				$n->markGray();
			} else {
				$n->buffered=false;
				unset($gs[$k]);
				if ($n->isErasable()){
					$n->delete();
				}
			}
		}
	}
	function ScanRoots(){
		foreach(array_keys(getSessionGlobal('bufferedRoots')) as $k){
			$GLOBALS['bufferedRoots'][$k]->scan();
		}
	}
	function CollectRoots(){
		$gs =& getSessionGlobal('bufferedRoots');
		foreach(array_keys($gs) as $k){
			$n =& $gs[$k];
			$n->buffered=false;
			unset($gs[$k]);
			$n->collectWhite();
		}
	}
	function markGray(){
		if ($this->color!='gray'){
			#@persistence_echo echo 'marking gray: '.$this->printString().'<br/>';@#
			$this->color='gray';
			$this->mapChild(
				#@lam &$t->	$t->refCount->decrement();$t->markGray();return $t;@#
    		);
		}
	}
	function scan(){
		if ($this->color=='gray'){
			if ($this->refCount->getValue()>0 || $this->rootObject->getValue()){
				$this->scanBlack();
			} else {
				$this->color='white';
				$this->mapChild(
					#@lam &$e->$e->scan();return $e;@#
				);
			}
		}
	}
	function scanBlack(){
		$this->color='black';
		#@persistence_echo echo 'not erasable: '.$this->printString().'<br/>';@#
		$this->mapChild(
			#@lam &$t->	$t->refCount->increment();if ($t->color!='black'){$t->scanBlack();}return $t;@#
   		);
	}
	function collectWhite(){
		if ($this->color=='white' and !$this->buffered){
			#@persistence_echo echo 'removing '.$this->printString().'<br/>';@#
			$this->color='black';
			$this->mapChild(
				#@lam &$e->$e->collectWhite();return $e;@#
			);
			$this->delete();
		};
	}
	function mapChild($fun){
		foreach($this->metadata->allFieldNames() as $f){
			$this->$f->mapChild($fun);
		}
	}
	function ResetRefCounts(){
		$DB=& DBSession::beginRegisteringAndTransaction();
		$obs =& new Collection;
		foreach(get_subclasses('PersistentObject') as $sc){
			$obs0 =& new PersistentCollection($sc);
			$obs0->for_each(lambda('&$elem', '$obs->atPut($elem->getInstanceId(), $elem);return $obs;', get_defined_vars()));
		}
		$obs->for_each(lambda('&$elem', '$elem->refCount->setValue(0);return $elem;'));
		$obs->for_each(lambda('&$elem', '$elem->mapChild(lambda(\'&$e\',\'$e->incrementRefCount();return $e;\'));return $elem;'));
		$DB->commit();
	}

}
?>