<?php

class FieldEditor extends Component {
	var $field;
	var $factory;

	function createInstance($params){
		$this->field =& $params['field'];
		$this->factory =& new EditorFactory;
	}
	function initialize() {
		$this->addComponent(new Label($this->field->displayString), 'fieldName');
		$widget =& $this->factory->createFor($this->field);
		$this->addComponent($widget, 'widget');
		$this->addComponent(new CommandLink(array('text' => 'Save', 'proceedFunction' => new FunctionObject($this, 'saveField'))), 'save');
		$this->addComponent(new CommandLink(array('text' => 'Cancel', 'proceedFunction' => new FunctionObject($this, 'cancel'))), 'cancel');
	}

	function saveField() {
		$this->field->commitChanges();
		$this->callback('field_edited', $this->field);
	}

	function cancel() {
    	if ($this->field->isModified()) {
    		$this->confirmCancel();
    	}
    	else
    		$this->cancelConfirmed();
    }

    function confirmCancel() {
    	$this->call($qd =& QuestionDialog::create('Are you sure you want to cancel your changes?'));
    	$qd->registerCallbacks(array('on_yes' => new FunctionObject($this, 'cancelConfirmed'), 'on_no' => new FunctionObject($this, 'cancelRejected')));
    }

    function cancelRejected() {

    }

    function cancelConfirmed() {
    	$this->field->flushChanges();
    	$this->callback('cancel');
    }
}
?>