<?php

require_once dirname(__FILE__) . '/../Component.class.php';

class NotificationDialog extends Component
{
	var $message;

	function NotificationDialog($message, $callback_actions=array('on_accept' => 'notification_accepted')) {
		parent::Component($callback_actions);
		$this->message = $message;
	}

	function configure() {
		return array('use_action_namemangling' => true);
	}

	function declare_actions() {
		return array('accept');
	}

	function render_on(&$html) {
		$html->text("<h1>" . $this->message . "</h1></br>");
		$html->begin_form_for_action('accept');
		$html->text("    <input type='submit'value=Accept />");
		$html->text("</form>");
	}

	function accept() {
		$this->callback('on_accept');
	}
}

?>