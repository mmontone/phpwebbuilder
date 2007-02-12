<?php

class PromptDialog extends Component
{
	var $message;
	var $text;

	function PromptDialog($message) {
		$this->message = $message;
		$this->text =& new ValueHolder('');

		parent::Component();
	}

	function &Create($message){
		if (Application::rendersAjax()) {
			return new ModalPromptDialog($message);
		} else {
			return new PromptDialog($message);
		}
	}

	function initialize() {
		$this->addComponent(new Label($this->message), 'msg');
		$this->addComponent(new Input($this->text), 'input');
	}

	function setText($text) {
		$this->text->setValue($text);
	}

	function accept() {
		$this->callbackWith('on_accept', $this->text->getValue());
	}

	function cancel() {
		$this->callback('on_cancel');
	}

	function onAccept(&$function) {
		$this->registerCallback('on_accept', $function);
		$this->addComponent(new CommandLink(array('text' => 'Accept', 'proceedFunction' => new FunctionObject($this, 'accept'))), 'accept');
	}

	function onCancel(&$function) {
		$this->registerCallback('on_cancel', $function);
		$this->addComponent(new CommandLink(array('text' => 'Cancel', 'proceedFunction' => new FunctionObject($this, 'cancel'))), 'cancel');
	}
}
?>