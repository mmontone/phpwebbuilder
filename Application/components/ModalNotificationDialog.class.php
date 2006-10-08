<?php

class AjaxComponent extends Component{
	function takeView(&$comp) {
		// We don't use a view. We are a "semantic" component
	}
}

class ModalNotificationDialog extends AjaxComponent {
	var $message;

    function ModalNotificationDialog($message) {
    	$this->message = $message;
    	parent::AjaxComponent();
    }


	function start() {
    	$app =& Application::instance();
    	$app->addAjaxCommand(new AjaxCommand('openNotificationDialog', array(toAjax($this->message), $this->getId(), constant('pwb_url'))));
    	$this->addInterestIn('accept', new FunctionObject($this, 'accept'));
    }

    function accept() {
    	$this->callback('on_accept');
    }

    function onAccept(&$function) {
		$this->registerCallback('on_accept', $function);
    }
}

class ModalErrorDialog extends AjaxComponent {
	var $message;

    function ModalErrorDialog($message) {
    	$this->message = $message;
    	parent::AjaxComponent();
    }


	function start() {
    	$app =& Application::instance();
    	$app->addAjaxCommand(new AjaxCommand('openErrorDialog', array(toAjax($this->message), $this->getId(), constant('pwb_url'))));
    	$this->addInterestIn('accept', new FunctionObject($this, 'accept'));
    }

    function accept() {
    	$this->callback('on_accept');
    }

    function onAccept(&$function) {
		$this->registerCallback('accept', $function);
    }
}

class ModalQuestionDialog extends AjaxComponent {
	var $message;

    function ModalQuestionDialog($message) {
    	$this->message = $message;
    	parent::AjaxComponent();
    }

    function getView() {
		// We don't use a view. We are a "semantic" component
    }

	function start() {
    	$app =& Application::instance();
    	$app->addAjaxCommand(new AjaxCommand('openQuestionDialog', array(toAjax($this->message), $this->getId(), constant('pwb_url'))));
    	$this->addInterestIn('yes', new FunctionObject($this, 'yes'));
    	$this->addInterestIn('no', new FunctionObject($this, 'no'));
    }

    function yes() {
    	$this->callback('on_yes');
    }

    function no() {
    	$this->callback('on_no');
    }
}

// Activates without ajax
class ModalQuestionDialog2 extends Component {
	var $message;

    function ModalQuestionDialog2($message) {
    	$this->message = $message;
    	parent::Component();
    }

    function getView() {
		// We don't use a view. We are a "semantic" component
    }

	function start() {
    	$app =& Application::instance();
    	$app->addAjaxCommand(new AjaxCommand('openQuestionDialog', array('message' => $this->message, 'callback_comp' => $this->getId())));
    	$this->addEventListener('yes', new FunctionObject($this, 'yes'));
    	$this->addEventListener('no', new FunctionObject($this, 'no'));
    }

    function yes() {
    	$this->callback('yes');
    }

    function no() {
    	$this->callback('no');
    }
}

class AjaxCommand {
	var $command;
	var $params;

	function AjaxCommand($command, $params) {
		$this->command = $command;
		$this->params = $params;
	}

	function renderAjaxResponseCommand() {
		$xml =  '<call f="' . $this->command . '">';
		$xml .= '<ps>';
		foreach ($this->params as $param) {
			$xml .= '<p>' . $param . '</p>';
		}
		$xml .='</ps></call>';
		return $xml;
	}
}

?>