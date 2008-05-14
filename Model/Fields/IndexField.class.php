<?php

class IndexField extends NumField {
	var $collection;
	var $nullValue;
	var $target = null;
	var $buffered_target = null;
	var $datatype;

	function IndexField($name, $isIndex=null, $dataType=null, $nullValue=null) {
		if (!is_array($isIndex) && !is_array($name)) {
			$ps = array('null_value'=>$nullValue,
						'type'=>$dataType,
						'is_index'=>$isIndex,
						'fieldName'=>$name);
			parent :: NumField($ps);
		} else {
			parent :: NumField($name, $isIndex);
		}
	}

   function printString(){
        return $this->debugPrintString();
    }

    function debugPrintString() {
    	return $this->primPrintString($this->owner->debugPrintString() . '>>' . $this->getName() . ' value: ' . $this->getValue() . ' target: ' . print_object($this->buffered_target));
    }

    function createInstance($params){
		parent::createInstance($params);
		$this->nullValue = & $params['null_value'];
		$this->collection = & new PersistentCollection($params['type']);
		$this->datatype =& $params['type'];
	}
	function & visit(& $obj) {
		return $obj->visitedIndexField($this);
	}

	function & obj() {
		return $this->getTarget();
	}

	function getDataType() {
		return $this->datatype;
	}
	function refreshId(){
		$this->changed();
	}

	function setTarget(& $target) {
		#@typecheck $target:PersistentObject@#
	    if (is_null($target)) {
	      $this->removeTarget();
	    return;
	    }

	    if (($this->buffered_target == null) or !($this->buffered_target->is($target))) {
	  $this->registerFieldModification();
            $this->removeTarget();
			$target->addInterestIn('id_changed', new FunctionObject($this, 'refreshId'), array('execute on triggering' => true));
            $this->buffered_target =& $target;
			$this->buffered_value = $target->getId();
            $target->incrementRefCount();
            $target->addedAsTarget($this->owner, $this->varName);
            $this->setModified(true);
            $this->triggerEvent('changed', $this);
            if ($this->owner->isPersisted()){
            	#@persistence_echo echo 'Registering sibling: ' . $this->owner->debugPrintString() . '>>' . $this->getName() . ' is ' . $target->debugPrintString().'<br/>';@#
            	$session =& DBSession::Instance();
				$session->saveIfModified($target);
            } else {
            #@persistence_echo echo 'NOT registering sibling: ' . $this->owner->debugPrintString() . '>>' . $this->getName() . ' is ' . $target->debugPrintString().'<br/>';@#
            }
        }
	}

    function removeTarget(){
      // We call get target because we need the target in memory
      // in order to register the modification when we set the target to null
      // -- marian
      $this->getTarget();
      if ($this->buffered_target !== null){
	        $this->registerFieldModification();

            $this->buffered_target->retractInterestIn('id_changed', new FunctionObject($this, 'refreshId'));
			$self =& $this;
			$this->mapChild(
				#@lam $e->$e->decrementRefCount();$e->removedAsTarget($self->owner, $self->varName);return $e;@#
			);
		}

		$n = null;
        $this->buffered_target =& $n;
		$this->setValue(0);
        // We have to set the buffered target in null because
		// a value setting sets the buffered target in null if
		// there has been a change in the value. Maybe setValue should
		// set the buffered target to null always?
	}
/*
    function &getModificationObject() {
    	$mo =& new IndexFieldModification($this);
    	return $mo;
    }
*/
	function registerCollaborators(){
		$t =& $this->getTarget();
		if ($t!=null){
			$t->registerPersistence();
		}
	}
	function getTargetId() {
		//return $this->buffered_target->getIdOfClass($this->collection->dataType);
		return $this->buffered_target->getIdOfClass($this->datatype);
	}

	function & getTarget() {
		if ($this->buffered_target == null) {
			$this->buffered_target =& $this->loadTarget();
		}

		return $this->buffered_target;
	}

	function &loadTarget() {
		//return $this->collection->getObj($this->getValue());
        $o =& PersistentObject::getWithId($this->datatype, $this->getValue());
		return $o;
	}

	/*function prepareToSave(){
		if ($this->buffered_target!=null){
			$this->buffered_target->save();
		}
	}*/
	function viewValue() {
		$obj = & $this->obj();
		if ($obj){
			return $obj->printString();
		} else {
			return '';
		}
	}

    function debugViewValue() {
        $obj = & $this->obj();
        if ($obj){
            return $obj->debugPrintString();
        } else {
            return '';
        }
    }

	function setValue($value) {
		if ($this->buffered_value != $value) {
            $n = null;
            $this->buffered_target =& $n;
            parent::setValue($value);
        }
	}

    function setReadValue($value) {
    	if (is_object($this->buffered_value)) {
    		print_backtrace_and_exit('Error: this should not have ocurred');
    	}
        parent::setReadValue($value);
    }

	function getValue() {
		if ($this->buffered_target != null) {
			$this->buffered_value = $this->getTargetId();
		}
		$v = parent::getValue();
		if ($v!=null){
			return $v;
		} else {
			return 0;
		}
	}

	/*
	function flushChanges() {
		parent::flushChanges();
		if ((is_object($this->buffered_target)) and (!($this->buffered_target->is($this->target)))) {
			$this->buffered_target =& $this->target;
			$this->triggerEvent('changed', $no_params = null);
		}
	}
	*/

	function commitChanges() {
		parent::commitChanges();
		$this->target =& $this->buffered_target;
	}

	function isEmpty() {
		return $this->getTarget() == null;
	}

    function isNull() {
    	return $this->isEmpty();
    }

	// Glue functions

    function &asValueModel() {
    	$aa =& new AspectAdaptor($this, 'Target');
		return $aa;
	}

    function &asTextHolder() {
    	$self =& $this;
        $pa =& new PluggableAdaptor(new LambdaObject('','$target =& $self->getTarget(); if ($target!==null) return $target->printString(); else return \'\';', get_defined_vars()),
                                    new LambdaObject('', 'print_backtrace_and_exit("Error: see IndexField>>asTextHolder");'));
        $this->addInterestIn('changed', new FunctionObject($pa, 'changed'));
        return $pa;
    }

    function assignResult(&$component) {
        #@typecheck $component : Component@#
        $component->setValueModel($this->asValueModel());
    }

    // End glue functions


    function SQLvalue() {
        if ($this->getValue() == 0) {
        	 #@gencheck
        	//print_backtrace('Warning!!: Index field sql value is 0. Field name: ' . $this->colName);
		    //@#
		    return "NULL, ";
        } else {
        	return parent::SQLvalue();
        }
    }
    //GARBAGE COLLECTION
    function mapChild($function){
		$t =& $this->getTarget();
		if ($t!=null){
			$function($t);
		}
	}
}

?>