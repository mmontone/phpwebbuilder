<?php

class QKTest extends PersistentObject{
    function initialize() {
    	$this->addField(new TextField(array('fieldName'=>'name', 'is_index'=> TRUE)));
    	$this->addField(new DateTimeField(array('fieldName'=>'timeStarted', 'is_index'=> TRUE)));
    	$this->addField(new DateTimeField(array('fieldName'=>'timeEnded')));
    	$this->addField(new NumField(array('fieldName'=>'totalPasses')));
    	$this->addField(new BoolField(array('fieldName'=>'passed', 'is_index'=> TRUE)));
    	$this->addField(new CollectionField(array('fieldName'=>'passes', 'type'=>'QKPass', 'reverseField'=>'test')));
    }
    function &lastPass(){
    	$c =& $this->passes->collection;
    	$c->orderBy('number', 'DESC');
    	$l = $c->limit;
    	$c->limit = 1;
    	$p =& $c->first();
    	$c->limit = $l;
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

class QKPass extends PersistentObject{
    function initialize() {
    	$this->addField(new TextArea(array('fieldName'=>'parameters')));
    	$this->addField(new HtmlArea(array('fieldName'=>'output')));
    	$this->addField(new NumField(array('fieldName'=>'number', 'is_index'=> TRUE)));
    	$this->addField(new IndexField(array('fieldName'=>'test', 'type'=>'QKTest', 'is_index'=> TRUE)));
    }
}


?>