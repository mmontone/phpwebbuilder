<?php

class QKTest extends PersistentObject{
    function initialize() {
    	$this->addField(new TextField(array('fieldName'=>'name', 'is_index'=> TRUE)));
    	$this->addField(new DateTimeField(array('fieldName'=>'timeStarted', 'is_index'=> TRUE)));
    	$this->addField(new DateTimeField(array('fieldName'=>'timeEnded')));
    	$this->addField(new NumField(array('fieldName'=>'totalPasses')));
    	$this->addField(new BoolField(array('fieldName'=>'passed', 'is_index'=> TRUE)));
    	$this->addField(new CollectionField(array('fieldName'=>'passes', 'type'=>'QKPass', 'reverseField'=>'test')));
    	$this->addField(new IndexField(array('fieldName'=>'function', 'type'=>'QKFunction')));
    }
    function &lastPass(){
    	$c =& $this->passes->collection;
    	$c->orderBy('number', 'DESC');
    	$p =& $c->first();
    	return $p;
    }
    function runAgain(){
    	new QuicKlickReprise($this);
    }
    function deleteTest(){
		$this->passes->collection->collect('delete()');
		$this->delete();
    }
}

class QKFunction extends PersistentObject{
    function initialize() {
    	$this->addField(new TextField(array('fieldName'=>'code')));
    }
    function getFun(){
    	return lambda('', $this->code->getValue());
    }
}


class QKPass extends PersistentObject{
    function initialize() {
    	$this->addField(new TextArea(array('fieldName'=>'parameters')));
    	$this->addField(new HtmlArea(array('fieldName'=>'output')));
    	$this->addField(new NumField(array('fieldName'=>'number', 'is_index'=> TRUE)));
    	$this->addField(new IndexField(array('fieldName'=>'test', 'type'=>'QKTest', 'is_index'=> TRUE)));
    }
}


?>