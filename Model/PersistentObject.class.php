<?
require_once dirname(__FILE__) . '/Model.class.php';
//require_once dirname(__FILE__) . '/../MYSQL.class.php' ;

class PersistentObject extends Model {
	/**
	 * @var PersistentObject The parent of this object (holds it's I.V.s)
	 */
	var $parent = NULL;
	/**
	 * Special var, for get_subclass
	 */
	var $isClassOfPWB = true;
	/**
	 * @var string The table this object is related to
	 */
	var $table;
	/**
	 * @var string The form that manages this object
	 * @deprecated
	 */
	var $formphp = "Action.php";
	/**
	 * @var Array(String) The names of the fields
	 */
	var $fieldNames = array ();
	/**
	 * @var Bool Controls if the object exists in the database
	 * @deprecated
	 */
	var $existsObject = FALSE;
	/**
	 * @var Array(String) The names of the index fields
	 */
	var $indexFields = array ();

	var $displayString;

	var $dirty = true;

	function & allFieldsThisLevel() {
		return $this->fieldsWithNames($this->allFieldNamesThisLevel());
	}
	function & allFields() {
		return $this->fieldsWithNames($this->allFieldNames());
	}
	function & fieldsWithNames($names) {
		$arr = array ();
		foreach ($names as $name) {
			$arr[$name] = & $this->fieldNamed($name);
		}
		return $arr;
	}
	function allFieldNames() {
		if ($this->isNotTopClass(get_class($this))) {
			if ($this->parent == null)
				backtrace();
			return array_merge($this->parent->allFieldNames(), $this->allFieldNamesThisLevel());
		}
		else {
			return $this->allFieldNamesThisLevel();
		}
	}
	function allIndexFieldNames() {
		if ($this->isNotTopClass(get_class($this))) {
			if ($this->parent == null)
				backtrace();
			return array_merge($this->parent->allIndexFieldNames(), $this->indexFields);
		}
		else {
			return $this->indexFields;
		}
	}
	function & allIndexFields() {
		return $this->fieldsWithNames($this->allIndexFieldNames());
	}
	function allFieldNamesThisLevel() {
		return $this->fieldNames;
	}
	function & fieldNamed($name) {
		$f = & $this-> $name;
		return $f;
	}
	function setField($name, & $field) {
		$this-> $name = & $field;
	}
	function & getField($f) {
		return $this-> $f;
	}
	function & getFields($fs) {
		$arr = array ();
		foreach ($fs as $f) {
			$arr[] = & $this->getField($f);
		}
		return $arr;
	}
	function addField(& $field) {
		$name = $field->colName;
		$this-> $name = & $field;
		$this->fieldNames[$name] = $name;
		if ($field->isIndex) {
			$this->indexFields[$name] = $name;
		}
		$field->owner = & $this;
		$field->addEventListener($this, $a = array (
			'change' => 'fieldChanged'
		));
	}

	function fieldChanged(& $field) {
		$this->triggerEvent('fieldChanged', $field);
	}

	function & findIndexField() {
		return $this->fieldsWithNames($this->indexFields);
	}
	function & field($s) {
		return $this-> $s;
	}
	function fieldID($s) {
		return $s;
	}
	/**
	 * @category Database
	 */
	/**
	 * @category ID
	 */
	function setID($id) {
		foreach ($this->allFieldsThisLevel() as $index => $field) {
			$this-> $index->setID($id);
		}
	}
	function getID() {
		$f = $this->id;
		return $f->getValue();

	}
    function getIdOfClass($class){
	    	if (strcasecmp(get_class($this), $class)==0) {
	    		return $this->id->value;
	    	} else {
	    		return $this->parent->getIdOfClass($class);
	    	}
    }
	/**
	 * @category Database
	 */
	function fieldNames($operation) {
		$fieldnames = "";
		foreach ($this->allFieldsAllLevels() as $name => $field) {
			$fieldnames .= $field->fieldName($operation);
		}
		$fieldnames = substr($fieldnames, 0, -2);
		return $fieldnames;
	}
	function allFieldsAllLevels(){
		$rcs = get_related_classes(get_class($this));
		$fs = array();
		foreach($rcs as $rc){
			if ($rc != 'persistentobject' && $rc != 'model' && $rc != 'pwbobject'){
				$o = new $rc;
				$fs = array_merge($fs, $o->allFields());
			}
		}
		$fs = array_merge($fs, $this->allFields());
		return $fs;
	}
	function loadFrom($reg) {
		foreach ($this->allFieldNamesThisLevel() as $index) {
			$field = & $this-> $index;
			$field->loadFrom($reg);
		}
		$this->setID($reg["id"]);
		$this->existsObject = TRUE;
	}
	function voidOption() {
		return $this->voidOption;
	}
	function tableName() {
		if ($this->table != "") {
			return baseprefix . $this->table;
		}
		else {
			return baseprefix . get_class($this);
		}
	}
	function tableNames(){
		$tns[] = $this->tableName();
		$rcs = get_related_classes(get_class($this));
		foreach($rcs as $rc){
			if ($rc != 'persistentobject' && $rc != 'model' && $rc != 'pwbobject'){
				$o = new $rc;
				$tns[] = $o->tableName();
			}
		}
		$tn = implode(', ',$tns);
		return $tn;
	}
	function idRelations(){
		return $this->tableName().'.id=' . $this->getID() . ' AND '.$this->idRestrictions();
	}
	function idRestrictions(){
		$rcs = get_related_classes($this);
		$rcs [] = get_class($this);
		$rss []='1=1';
		foreach($rcs as $rc){
			$sup = get_parent_class($rc);
			if ($sup != 'persistentobject' && $sup != 'model' && $sup != 'pwbobject' && $sup != ''){
				$o1 = new $rc;
				$o2 = new $sup;
				$rss[] = $o2->tableName().'.id = '.$o1->tableName().'.super';
			}
		}
		return implode(' AND ', $rss);
	}
	function basicLoad() {
		$sql = 'SELECT ' . $this->fieldNames('SELECT') . ' FROM ' . $this->tableNames() . ' WHERE '.$this->idRelations(). ';';
		$db = new mysqldb;
		$record = $db->fetchRecord($db->SQLExec($sql, FALSE, $this));
		$this->loadFrom($record);
		$this->existsObject = TRUE;
	}
	function basicInsert() {
		$values = '';
		foreach ($this->allFieldsThisLevel() as $index => $field) {
			$values .= $field->insertValue();
		}
		$values = substr($values, 0, -2);
		$sql = 'INSERT IGNORE INTO ' . $this->tableName() . '(' . $this->fieldNames('INSERT') . ') VALUES ('.$values.')';
		$db = new mysqldb;
		$db->SQLExec($sql, TRUE, & $this, & $rows);
		$this->existsObject = TRUE;
		return $rows > 0;
	}
	function updateString() {
		$values = '';
		foreach ($this->allFieldsThisLevel() as $index => $field) {
			$values .= $field->updateString();
		}
		$values = substr($values, 0, -2);
		return "UPDATE " . $this->tableName() . " SET $values WHERE id=" . $this->getID();

	}
	function basicUpdate() {
		$sql = $this->updateString();
		$db = new mysqldb;
		$db->SQLExec($sql, FALSE, $this, &$rows);
		$this->existsObject = TRUE;
		return $rows > 0;
	}
	function basicDelete() {
		$this->load();
		$sql = "DELETE FROM " . $this->tableName() . " WHERE id=" . $this->getId();
		$db = new mysqldb;
		$can = TRUE;
		foreach ($this->allFieldsThisLevel() as $f) {
			$can = $can & $f->canDelete();
		}
		if ($can)
			$db->SQLExec($sql, FALSE, $this);
		else {
			trace("The object is not erasable<BR>\n");
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
	/**
	 * @category Validation
	 */
	function checkNotEmpty($fields, & $error_msgs) {
		$ret = true;
		$is_valid = false;
		foreach ($fields as $field) {
			$is_valid = $this-> $field->value != "";
			if (!$is_valid) {
				$error_msgs[$field] = "Fill in the " . $field . ", please";
			}
			$ret &= $is_valid;
		}
		return $ret;
	}

	/*@deprecated*/
	function check_not_null($fields, & $error_msgs) {
		return $this->checkNotEmpty($fields, $error_msgs);
	}

	function checkOneOf($fields, $error_msg, & $error_msgs) {
		$ret = false;
		foreach ($fields as $field) {
			if (!isset ($first_field))
				$first_field = $field;
			$ret |= $this-> $field->value != "";
		}
		if (!$ret) {
			$error_msgs[$first_field] = $error_msg;
		}
		return $ret;
	}
	function validate(& $error_msgs) {
		return true;
	}

	/* Population */
	function populate($form, & $error_msgs) {
		$success = true;
		foreach ($this->allFieldNames() as $index) {
			// Populate the object "allFields()"
			$field = & $this-> $index;
			$success = $success && $this->populateField($field, $form, $error_msgs);
		}
		// Check object
		$success = $success && $this->obj->validate(& $error_msgs);
		//if (!$success) trace(print_r($error_msgs, TRUE));
		return $success;
	}

	function populateField(& $field, & $form, & $error_msgs) {
		// Checks the field data
		if (!$field->populate($form)) {
			$error_msgs[$field->colName] = "The " . $field->colName . " is invalid";
			return false;
		}
		return true;
	}

	function toArray() {
		$arr = array ();
		foreach ($this->allFields() as $index => $field) {
			$arr[$index] = $field->toArrayValue();
		}
		return $arr;
	}
	/**
	 * Creates an object from the sepcified class, and completes the
	 *    hierarchy all the way up.
	 */
	/**
	 * @category Inheritance
	 */

	function isNotTopClass(& $class) {
		return (strcasecmp(get_parent_class($class), 'PersistentObject') != 0 && strcasecmp(get_parent_class($class), 'ObjSQL') != 0 && strcasecmp(get_parent_class($class), 'Model') != 0);
	}
	/**
	 * Load an objects subclasses if exist
	 */
	function & getWithIdChildren($id) {
		$class = get_class($this);
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
		$class = get_class($this);
		if ($this->isNotTopClass($class)) {
			/* We have to load all the way up */
			$parclass = get_parent_class($class);
			$par = & new $parclass;
			$parid = $this->super->value;
			trace("Loading parent from " . $parid . " for ");
			$par->loadFromId($parid);
			$p = & $par->getWithIdParent();
			$this->setParent($p);
			return $this;
		}
		else {
			return $this;
		}
	}
	/**
	 * @category Creation
	 */
	function PersistentObject() {
		$this->createInstance();
	}
	function & createInstance() {
		if ($this->isNotTopClass($this)) {
			$this->setParent($this->create(get_parent_class(get_class($this))));
		}
		$this->basicInitialize();
		return $this;
	}
	function & create($class) {
		return new $class;
	}
	function basicInitialize() {
		$this->addField(new idField("id", FALSE));
		if ($this->isNotTopClass($this)) {
			$this->addField(new superField("super", FALSE));
		}
		$this->displayString = ucfirst(get_class($this));
		$this->initialize();
	}
	function initialize() {}
	/**
	 * @category Persistence
	 */
	/**
	 * Function for loading an object (class method)
	 */
	function & getWithId($class, $id) {
		$obj = & new $class;
		$obj = & $obj->loadFromId($id);
		$par = & $obj->getWithIdParent($id);
		$sub = & $obj->getWithIdChildren($id);
		return $sub;
	}
	/**
	 * Loads an object from an id, not worrying about inheritance (class)
	 */
	function & loadFromId($id) {
		$this->setID($id);
		$this->basicLoad();
		$this->getWithIdParent($this->id->value);
		return $this;
	}
	/**
	 * Loads an object from an id, all the way up
	 */
	function & load() {
		$this->loadFromId($this->id->value);
		return $this;
	}
	function save() {
		if ($this->existsObject) {
			return $this->update();
		}
		else {
			return $this->insert();
		}
	}
	function update() {
		if ($this->isNotTopClass($this)) {
			$p = & $this->getParent();
			$p->id->value = $this->super->value;
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
			$this->super->value = $p->id->value;
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

	/* Helper methods for fields addition */

	function addTextField($name, $params = array ()) {
		$this->addField(new TextField($name, $params));
	}

	function addPasswordField($name, $params = array ()) {
		$this->addField(new PasswordField($name, $params));
	}

	function addTextArea($name, $params = array ()) {
		$this->addField(new TextArea($name, $params));
	}

	function addIndexField($name, $params = array ()) {
		$params['is_index'] = true;
		$this->addField(new IndexField($name, $params));
	}

	function addCollectionField($name, $params = array ()) {
		$this->addField(new CollectionField($name, $params));
	}

	function addNumField($name, $params = array ()) {
		$this->addField(new NumField($name, $params));
	}

	function addBoolField($name, $params = array ()) {
		$this->addField(new BoolField($name, $params));
	}
}
?>