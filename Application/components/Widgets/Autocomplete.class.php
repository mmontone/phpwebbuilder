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
        $this->textDisplayed =& new ValueHolder($this->displayF->callWith($this->value_model->getValue()));
        $this->addComponent(new Input($this->textDisplayed),"displayText");
        $this->textDisplayed->onChangeSend("textChanged", $this);
        $this->addComponent(new Component(),"listDisplay");
        $elementId =& new Input($null);
        $this->addComponent($elementId,"elementId");

        $elementId->value_model->onChangeSend('valueChanged', $this);
        $win =& $this->getWindow();
        $win->addAjaxCommand(new AjaxCommand('autocomplete', array($this->getId())));
    }
    function textChanged(){
        $elems =& $this->getElements($this->displayText->getValue());

        $this->value_model->setValue($elems->first());
    }
    function valueChanged(){
        $this->value_model->setValue($this->options->at($this->elementId->getValue()));
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
    function &getValue(){
        return $this->value_model->getValue();
    }
	function &printPrimitive(&$primitive) {
		return $primitive;
	}
    function getElements($value){
        $cc =& new CompositeReport($this->options);
        $cc->setCondition( $this->filterField, 'LIKE', "'$value%'");
        $cc->setLimit(15);
        return $cc;

    }

}

?>