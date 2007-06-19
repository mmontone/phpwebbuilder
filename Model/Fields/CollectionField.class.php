<?php
class CollectionField extends DataField {
	var $collection;
	var $fieldname;
	var $type;

	function CollectionField($name, $dataType = array ()) {
	    if (is_array($name)) {
			parent :: DataField($name);
		} else {
			if (is_array($dataType)) {
				$dataType['reverseField'] = $name;
				parent :: DataField($dataType);
			} else {
				parent :: DataField(array (
					'reverseField' => $name,
					'type' => $dataType
				));
			}
        }
	}

    function addResult(&$component) {
    	#@typecheck $component : Component@#
        $component->setValueModel(new PluggableAdaptor(new FunctionObject($this, 'add'), new FunctionObject($this, 'shouldNotImplement')));
    }

    function getReverseField() {
    	return $this->creationParams['reverseField'];
    }

    function &getCollection() {
    	return $this->collection;
    }

	function getDataType() {
		return $this->collection->getDataType();
	}

    function getJoinDataType() {
        return $this->type->getJoinDataType();
    }

	function registerCollaborators() {
		$this->collection->collect('registerPersistence()');
	}
	function createInstance($params) {
		parent :: createInstance($params);

        if (!isset($params['direct']) or ($params['direct'] == 'yes')) {
        	$this->type =& new DirectCollectionFieldType($this);
        }
        else {
        	$this->type =& new IndirectCollectionFieldType($this);
        }

        return $this->type->createInstance($params);
	}
	function addFromEvent(& $triggerer, &$elem){
		return $this->add($elem);
	}
	function removeFromEvent(& $triggerer, &$elem){
		return $this->remove($elem);
	}
	function add(& $elem) {
	  $validation_method = 'validate' . ucfirst(substr($this->getName(), 0, strlen($this->getName()) - 1)) . 'Addition';
	  $this->owner->$validation_method($elem);
	  $current_component =& getdyn('current_component');
	  if (is_object($current_component)) {
	    $current_component->registerFieldModification(new CollectionFieldAddition($this, $elem));
	  }
	  else {
	    #@tm_echo echo 'Not registering addition of ' . $elem->debugPrintString() . ' to ' . $this->debugPrintString()  .'<br/>';@#
	     }
	  return $this->type->add($elem);
	}

	function remove(& $elem) {
	  $current_component =& getdyn('current_component');
	  if (is_object($current_component)) {
	    $current_component->registerFieldModification(new CollectionFieldRemoval($this, $elem));
	  }
	  else {
	    #@tm_echo echo 'Not registering removal of ' . $elem->debugPrintString() . ' to ' . $this->debugPrintString()  .'<br/>';@#
	     }
	  return $this->type->remove($elem);
	}

	function removedAsTarget(& $elem, $field) {
		if ($this->creationParams['reverseField'] == $field && $elem->hasType($this->creationParams['type'])) {
			$elem->decrementRefCount();
		}
	}
	function addedAsTarget(& $elem, $field) {
		if ($this->creationParams['reverseField'] == $field && $elem->hasType($this->creationParams['type'])) {
			$elem->incrementRefCount();
			if ($this->owner->isPersisted()){
            	#@persistence_echo echo 'Registering sibling: ' . $this->owner->debugPrintString() . '>>' . $this->getName() . ' is ' . $target->debugPrintString().'<br/>';@#
            	$elem->registerPersistence();
            } else {
            	#@persistence_echo echo 'NOT registering sibling: ' . $this->owner->debugPrintString() . '>>' . $this->getName() . ' is ' . $target->debugPrintString().'<br/>';@#
            }
		}
	}

	function defaultValues($params) {
		$v = array ('fieldName' => $params['type'] . $params['reverseField']);

		return array_merge($v, parent :: defaultValues(array_merge($params, $v)));
	}

	function fieldNamePrefixed($prefix) {

	}

	function & visit(& $obj) {
		return $obj->visitedCollectionField($this);
	}

	function setID($id) {
		$this->setValue($id);

		$this->type->setID($id);

	}
	function SQLvalue() {
	}
	function updateString() {
	}
	function loadFrom(& $reg) {
		/*
        $this->collection->refresh();*/
		return true;
	}
	function & elements() {
		return $this->collection->elements();
	}

	function setValue($value) {
		// Don't register modification
		$this->buffered_value = $value;
	}

	function canDelete() {
		// Note: we should be using CollectionIterator instead of Collections
		// If we modify this collection with setCondition, then this will not work
		// This field collection should be inmutable
		return $this->collection->isEmpty();
	}

    function getTargetField() {
    	return $this->type->getTargetField();
    }

	//GARBAGE COLLECTION
	function mapChild($function) {
		$this->collection->for_each($function);
	}

    function isDirect() {
    	return $this->type->isDirect();
    }

    function initialize() {
    	$this->type->initialize();
    }

    function printString() {
        if ($this->isDirect()) {
        	$t = ' (direct)';
        }
        else {
        	$t = ' (indirect)';
        }
        return $this->primPrintString($this->colName . ' type:' . $this->getDataType() . $t);
    }
}

class CollectionFieldType {
    var $collection_field;

    function CollectionFieldType(&$collection_field) {
    	$this->collection_field =& $collection_field;
    }
}

class DirectCollectionFieldType extends CollectionFieldType {
    var $target_type;
    var $reverseField;

    function createInstance($params) {
    	#@gencheck
        if (!isset($params['type'])) {
            print_backtrace_and_exit('Specify type for ' . $this->collection_field->printString());
        }

        if (!isset ($params['reverseField'])) {
            print_backtrace_and_error('No reverse field');
        }//@#
    }

    function setID($id) {
    	$this->collection_field->collection->setCondition($this->collection_field->creationParams['reverseField'], '=', $id);
    }

    function initialize() {
    	$this->collection_field->collection = & new PersistentCollection($this->collection_field->creationParams['type']);
        $this->collection_field->collection->setCondition($this->collection_field->creationParams['reverseField'], '=', '-1');
    }

    function add(&$elem) {
        if ($this->collection_field->owner->isPersisted()) {
        	// Now, the explanation for doing a save to the DB now.
		// As we are handling collections of objects directly from the DB we have to make collection changes
		// immediatly observable. That means we have to do a query to the DB each time we modify the collection.
		// There two consecuences:
		// 1) We don't have in-memory collections at the moment. That means we cannot modify a non-saved object's collection.
		//    This one does not have a solution right now. We would have to be able to access in-memory collections and
		//    db-collection with the same interface (the Report interface basically)
		// 2) We need to start a transaction if it was not started before. We prefer doing everything in a transaction.
		//    We wouldn't like to call "beginTransaction" explicitly as
		// "we prefer doing everything in a transaction" and "there are not nested transactions" -> "we begin the transaction before doing any insert/update to the database automatically"
		// The programmer is still in charge of committing everything before the request finishes.
		// I wish I had time to implement in-memory collections.
		//                                          -- marian
		$elem->registerForPersistence();
		$elem->{$this->collection_field->getReverseField()}->setTarget($this->collection_field->getOwner());
		$elem->incrementRefCount();

                // This "commit" may seem to be tricky. We are not doing commitMemoryTransaction because that is not our
		// purpose. We don't want to discard rollbackable commands. The transaction is not finished. We only
		// want to save registered objects to the database.
		$comp =& getdyn('current_component');
		$comp->saveMemoryTransactionObjects();
		
		$this->collection_field->collection->refresh();
        }
	else {
	  print_backtrace_and_exit('Sorry: in-memory persistent collections not implemented. ' . $this->collection_field->owner->debugPrintString() . ' is not' .
	                           ' persisted and so we cannot reference it from the database');
	}
    }

    function remove($elem) {
    	$elem-> {
            $this->collection_field->creationParams['reverseField'] }
        ->removeTarget();
        $elem->decrementRefCount();
	//$db =& DBSession::Instance();
	//$db->save($elem);
	$comp =& getdyn('current_component');
	$comp->saveMemoryTransactionObjects();
        $this->collection_field->collection->refresh();
    }

    function isDirect() {
    	return true;
    }
}

class IndirectCollectionFieldType extends CollectionFieldType {
    function getJoinDataType() {
        return $this->collection_field->creationParams['joinType'];
    }

    function getReverseField() {
    	return $this->collection_field->creationParams['reverseField'];
    }

    function getTargetField() {
    	return $this->collection_field->creationParams['targetField'];
    }

    function createInstance($params) {
        #@gencheck
        if (!isset($params['targetField'])) {
        	$this->targetField = 'target';
        }
        else {
            $this->targetField = $params['targetField'];
        }

        if (!isset($params['reverseField'])) {
        	$this->reverseField = 'owner';
        }
        else {
            $this->reverseField = $params['reverseField'];
        }

        if (!isset($params['joinType'])) {
        	print_backtrace_and_exit('Specify join type for ' . $this->collection_field->owner->debugPrintString());
        }//@#
    }

    function initialize() {
        $this->collection_field->collection = & $this->buildReport();
    }

    function &buildReport() {
    	$join_metadata =& PersistentObjectMetaData::getMetaData($this->getJoinDataType());
        $target_field =& $join_metadata->getFieldNamed($this->getTargetField());

        $owner =& $this->collection_field->getOwner();

        $r =& new Report(array('class' => $target_field->getDataType()));

        $r->defineVar('_joinVar', $this->getJoinDataType());
        $r->setPathCondition(new EqualCondition(array('exp1' => new AttrPathExpression('_joinVar', $this->getTargetField()),
                                                      'exp2' => new ObjectPathExpression('target'))));

        $r->setPathCondition(new EqualCondition(array('exp1' => new AttrPathExpression('_joinVar', $this->getReverseField()),
                                                      'exp2' => new ObjectExpression($owner))));
        return $r;
    }

    function setID($id) {
        // TODO: implement Report in data-flow style (using ValueHolders) so that it gets
	// automatically updated. We rebuild it for now.
        $this->collection_field->collection = & $this->buildReport();
    }

    function add(&$elem) {
        if ($this->collection_field->owner->isPersisted()) {
		// Now, the explanation for doing a save to the DB now.
		// As we are handling collections of objects directly from the DB we have to make collection changes
		// immediatly observable. That means we have to do a query to the DB each time we modify the collection.
		// There two consecuences:
		// 1) We don't have in-memory collections at the moment. That means we cannot modify a non-saved object's collection.
		//    This one does not have a solution right now. We would have to be able to access in-memory collections and
		//    db-collection with the same interface (the Report interface basically)
		// 2) We need to start a transaction if it was not started before. We prefer doing everything in a transaction.
		//    We wouldn't like to call "beginTransaction" explicitly as
		// "we prefer doing everything in a transaction" and "there are not nested transactions" -> "we begin the transaction before doing any insert/update to the database automatically"
		// The programmer is still in charge of committing everything before the request finishes.
		// I wish I had time to implement in-memory collections.
		//                                          -- marian
		$jdt = $this->getJoinDataType();
		$joinObject =& new $jdt;
		$joinObject->registerForPersistence();
	        $joinObject->{$this->getReverseField()}->setTarget($this->collection_field->getOwner());
		$joinObject->{$this->getTargetField()}->setTarget($elem);
		$comp =& getdyn('current_component');
		$comp->saveMemoryTransactionObjects();
        }
	else {
	  print_backtrace_and_exit('Sorry: in-memory persistent collections not implemented. ' . $this->collection_field->owner->debugPrintString() . ' is not' .
	                           ' persisted and so we cannot reference it from the database');
	}


        $this->collection_field->collection->refresh();
    }

    function remove(&$elem) {
        $owner =& $this->collection_field->getOwner();

        $r =& new Report(array('class' => $this->getJoinDataType()));
        $r->defineVar('_joinTarget', $this->collection_field->getDataType());

        $r->setPathCondition(new EqualCondition(array('exp1' => new AttrPathExpression('target', $this->getTargetField()),
                                                      'exp2' => new ObjectPathExpression('_joinTarget'))));

        $r->setPathCondition(new EqualCondition(array('exp1' => new AttrPathExpression('target', $this->getReverseField()),
                                                      'exp2' => new ObjectExpression($owner))));

        if ($r->isEmpty()) {
        	$ex =& new PWBException(array('message' => 'Error removing object: ' . $elem->debugPrintString() . ' does not belong to ' . $this->collection_field->printString()));
            return $ex->raise();
        }
        else {
	     $joinObject =& $r->first();
	     $joinObject->{$this->getReverseField()}->getTarget();
	     $joinObject->{$this->getReverseField()}->removeTarget();
	     $joinObject->{$this->getTargetField()}->removeTarget();
	     //$db =& DBSession::Instance();
	     // We assume there are not other references to JoinObjects so we can safely remove it from the db
	     //$db->delete($joinObject);
	     $comp =& getdyn('current_component');
	     $comp->saveMemoryTransactionObjects();
	     $this->collection_field->collection->refresh();
        }
    }

    function isDirect() {
    	return false;
    }
}
?>