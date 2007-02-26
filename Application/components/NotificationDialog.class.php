<?php

class NotificationDialog extends Component
{
	var $message;

	function NotificationDialog($message) {
		$this->message = $message;
		parent::Component();
	}

	function &Create($message){
		if (Application::rendersAjax()) {
			$nd =& new ModalNotificationDialog($message);
		} else {
			$nd =& new NotificationDialog($message);
		}
		return $nd;
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