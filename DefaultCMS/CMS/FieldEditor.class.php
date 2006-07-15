<?php

class FieldEditor extends Component {
	var $field;
	var $factory;

	function FieldEditor($params) {
		$this->field =& $params['field'];
		$this->factory =& new EditorFactory;
		parent::Component();
	}

	function initialize() {
		$this->addComponent(new Label($this->field->displayString), 'fieldName');
		$this->addComponent($this->factory->createFor($this->field), 'widget');
		$this->addComponent(new CommandLink(array('text' => 'Save', 'proceedFunction' => new FunctionObject($this, 'saveField'))), 'save');
		$this->addComponent(new CommandLink(array('text' => 'Cancel', 'proceedFunction' => new FunctionObject($this, 'cancel'))), 'cancel');
	}

	function saveField() {
		$this->field->commitChanges();
		$this->callback('refresh');
	}

	function cancel() {
    	if ($this->field->isModified()) {
    		$this->confirmCancel();
    	}
    	else
    		$this->cancelConfirmed();
    }

    function confirmCancel() {
    	$this->call(new QuestionDialog('Are you sure you want to cancel your changes?', array('on_yes' => new FunctionObject($this, 'cancelConfirmed'), 'on_no' => new FunctionObject($this, 'cancelRejected'))));
    }

    function cancelRejected() {

    }

    function cancelConfirmed() {
    	$this->field->flushChanges();
    	$this->callback('cancel');
    }
}
?>