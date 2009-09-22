<?php

class Autocomplete extends Component {
    var $options;
	var $displayF;
	var $selected_index = -1;
	var $size = 1;
    function Autocomplete(&$value_model, &$collection, $filterField, $displayF=null) {
        #@typecheck $collection:Collection@#
        $this->value_model =& $value_model;
        $this->filterField=$filterField;
        parent::Component();
    	$this->options =& $collection;

    	if ($displayF!=null){
    		$this->displayF=$displayF;
    	} else if ($collection->hasObjects()){
    		$this->displayF =& new FunctionObject($this, 'printObject');
    	} else {
    		$this->displayF =& new FunctionObject($this, 'printPrimitive');
    	}
    }
    function initialize(){
        parent::initialize();
        $this->addComponent(new Input(new ValueHolder($this->displayF->callWith($this->value_model->getValue()))),"displayText");
        $this->addComponent(new Component(),"listDisplay");
        $elementId =& new Input($null);
        $elementId->beHidden(true);
        $this->addComponent($elementId,"elementId");

        $this->elementId->onChangeSend('valueChanged', $this);
        $win =& $this->getWindow();
        $win->addAjaxCommand(new AjaxCommand('autocomplete', array($this->getId())));
    }
    function valueChanged(){
        $this->value_model->setValue($collection->at($this->elementId->getValue()));
    }
    function &printObject(&$object) {
        if ($object!=null){
            $str = $object->printString();
            return $str;
        } else {
            $n="";
            return $n;
        }
	}

	function &printPrimitive(&$primitive) {
		return $primitive;
	}
    function getElements($value){
        $cc =& new CompositeReport($this->options);
        $cc->setCondition( $this->filterField, 'LIKE', "$value%");
        return $cc;

    }

}

?>