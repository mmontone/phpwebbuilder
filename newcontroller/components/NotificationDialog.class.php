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
		$this->add_component(new Text("<h1>"));
		$this->add_component(new Text($this->message));
		$this->add_component(new Text("</h1></br>"));
		$this->add_component(new ActionLink($this, 'accept', 'Accept'), "accept");
	}

	function accept() {
		$this->callback('on_accept');
	}
}

?>