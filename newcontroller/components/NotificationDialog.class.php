<?php

require_once dirname(__FILE__) . '/../Component.class.php';

class NotificationDialog extends Component
{
	var $message;

	function NotificationDialog($message, $callback_actions=array('on_accept' => 'notification_accepted')) {
		$this->message = $message;
		parent::Component($callback_actions);
	}

	function configure() {
		return array('use_action_namemangling' => true);
	}

	function declare_actions() {
		return array('accept');
	}

	function initialize() {
	/*	$this->addComponent(new Text("<h1>"));
		$this->addComponent(new Text($this->message));
		$this->addComponent(new Text("</h1></br>"));*/
		$this->addComponent(new ActionLink($this, 'accept', 'Accept'), "accept");
	}

	function accept() {
		$this->callback('on_accept');
	}
}

?>