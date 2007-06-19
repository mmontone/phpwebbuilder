<?php

  // Both RootObjectsCollection and CollectionField preserve a similar interface.
  // When we admin objects in general we want to admin either RootObjects or and object's collection (a CollectionField)

class RootObjectsCollection {
  var $collection;
  
  function RootObjectsCollection(&$collection) {
    $this->collection =& $collection;
  }

  function getDataType() {
    return $this->collection->getDataType();
  }

  function &getCollection() {
    return $this->collection;
  }
  
  function add(&$object) {
    $this->validateObjectAddition($object);
    $object->makeRootObject();
    $current_component =& getdyn('current_component');
    if (is_object($current_component)) {
      $current_component->registerFieldModification(new RootObjectsCollectionAddition($this, $object));
    }
    else {
      #@tm_echo echo 'Not registering addition of ' . $object->debugPrintString() . ' to ' . $this->debugPrintString()  .'<br/>';@#
    }
  }

  function validateObjectAddition(&$object) {
    // TODO: implement generic functionality here instead of saying "subclass responsablity".
    // Take object indexfields and generate the validation query
    print_backtrace_and_exit('Subclass responsibility');
  }

  function remove(&$object) {
    $current_component =& getdyn('current_component');
    if (is_object($current_component)) {
	    $current_component->registerFieldModification(new RootObjectsCollectionRemoval($this, $object));
	  }
	  else {
	    #@tm_echo echo 'Not registering addition of ' . $object->debugPrintString() . ' to ' . $this->debugPrintString()  .'<br/>';@#
 }
    $object->deleteRootObject();
  }

  function debugPrintString() {
    return print_object($this, ' collection: ' . print_object($this->collection));
  }
}

?>