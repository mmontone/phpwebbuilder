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

	function add(& $elem) {
	   return $this->type->add($elem);
	}

	function remove(& $elem) {
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
    	$elem-> {
            $this->collection_field->getReverseField()}
        ->setTarget($this->collection_field->getOwner());
        if ($this->collection_field->owner->isPersisted()) {
            $elem->registerPersistence();
        }
        $this->collection_field->collection->add($elem);
        $elem->incrementRefCount();
    }

    function remove($elem) {
    	$elem-> {
            $this->collection_field->creationParams['reverseField'] }
        ->removeTarget();
        $elem->decrementRefCount();
        $this->collection_field->collection->remove($elem);
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
        	print_backtrace_and_exit('Specify join type for ' . $this->collection_field->printString());
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
        // TODO: estaria bueno usar ValueHolders para las partes variantes de un report. Cada vez
        // que un valor cambia, el report se evalua nuevamente.
        // Por el momento, genero de vuelta el report.
        $this->collection_field->collection = & $this->buildReport();
    }

    function add(&$elem) {
        // Esto no anda
        $joinObject =& new $this->getJoinDataType();
        $joinObject->{$this->getReverseField()}->setTarget($this->collection_field->getOwner());
        $joinObject->{$this->getTargetField()}->setTarget($elem);
        if ($this->collection_field->owner->isPersisted()) {
            $joinObject->registerPersistence();
        }
        $this->collection_field->collection->add($elem);
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
        	$ex =& new PWBException(array('message' => 'Error removing object: ' . $elem->printString() . ' does not belong to ' . $this->collection_field->printString()));
            return $ex->raise();
        }
        else {
            $joinObject =& $r->first();
            $n = null;
            $joinObject->{$this->getReverseField()}->removeTarget();
            $joinObject->{$this->getTargetField()}->removeTarget();
            $joinObject->decrementRefCount();

            return false;
        }
    }

    function isDirect() {
    	return false;
    }
}
?>