<?php

class NotificationDialog extends Component
{
	var $message;

	function NotificationDialog($message, $callback_actions=array('on_accept' => 'notification_accepted')) {
		$this->message = $message;
		parent::Component($callback_actions);
	}

	function initialize() {
		$this->addComponent(new Label($this->message), 'message_label');
		$this->addComponent(new CommandLink(array('text' => 'Accept', 'proceedFunction' => new FunctionObject($this, 'accept'))), 'accept_link');
	}

	function accept() {
		$this->callback('on_accept');
	}
}

?>