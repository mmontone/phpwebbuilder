<?php

class ErrorDialog extends Component
{
	var $message;

	function ErrorDialog($message, $callback_actions=array('on_accept' => 'notification_accepted')) {
		$this->message = $message;
		parent::Component($callback_actions);
	}

	function initialize() {
		$this->addComponent(new Label($this->message), 'error');
		$this->addComponent(new CommandLink(array('text' => 'Accept', 'proceedFunction' => new FunctionObject($this, 'accept'))), 'accept');
	}

	function accept() {
		$this->callback('on_accept');
	}
}
?>