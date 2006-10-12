<?php

class NotificationDialog extends Component
{
	var $message;

	function NotificationDialog($message) {
		$this->message = $message;
		parent::Component();
		//$this->registerCallbacks($callback_actions);
	}
	function &create($message){
		if (constant('page_renderer') == 'AjaxPageRenderer') {
			return new ModalNotificationDialog($message);
		} else {
			return new NotificationDialog($message);
		}
	}
	function initialize() {
		$this->addComponent(new Label($this->message), 'notification');

	}

	function accept() {
		$this->callback('on_accept');
	}

	function onAccept(&$function) {
		$this->registerCallback('on_accept', $function);
		$this->addComponent(new CommandLink(array('text' => 'Accept', 'proceedFunction' => new FunctionObject($this, 'accept'))), 'accept');
	}
}

?>