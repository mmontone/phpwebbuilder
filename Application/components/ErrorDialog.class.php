<?php

class ErrorDialog extends Component
{
	var $message;

	function ErrorDialog($message) {
		$this->message = $message;
		parent::Component();
	}
	function &create($message){
		if (Application::rendersAjax()) {
			$ed =& new ModalErrorDialog($message);
		} else {
			$ed =& new ErrorDialog($message);
		}
		return $ed;
	}
	function initialize() {
		$this->addComponent(new Label($this->message), 'error');
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