<?php

class AjaxComponent extends Component{
	function takeView(&$comp) {
		// We don't use a view. We are a "semantic" component
	}
	function replaceView(&$comp) {
		// We don't use a view. We are a "semantic" component
	}
    function stopAndCall(&$component) {
		$this->basicCall($component);
    }
    function renderMessage() {
		if (is_object($this->message)) {
			return toAjax($this->message->printString());
		} else {
			return toAjax($this->message);
		}
	}
}

class ModalNotificationDialog extends AjaxComponent {
	var $message;

    function ModalNotificationDialog($message) {
    	$this->message = $message;
    	parent::AjaxComponent();
    }


	function start() {
    	$app =& Window::getActiveInstance();
    	$app->addAjaxCommand(new AjaxCommand('openNotificationDialog', array($this->renderMessage(), $this->getId(), constant('pwb_url'))));
    	$this->addInterestIn('accept', new FunctionObject($this, 'accept'));
    }

    function accept() {
    	$this->callback();
    	$this->on_accept->call();
    }

    function onAccept(&$function) {
		$this->on_accept=&$function;
    }
}

class ModalPromptDialog extends AjaxComponent {
	var $message;
	var $text;

    function ModalPromptDialog($message) {
    	$this->message = $message;
    	$this->text =& new ValueHolder('Ingrese aqui');
    	parent::AjaxComponent();
    }

    function setText($text) {
    	$this->text->setValue($text);
    }

	function start() {
    	$app =& Window::getActiveInstance();
    	$app->addAjaxCommand(new AjaxCommand('openPromptDialog', array($this->renderMessage(), toAjax($this->text->getValue()), $this->getId(), constant('pwb_url'))));
    	$this->addInterestIn('accept', new FunctionObject($this, 'accept'));
    	$this->addInterestIn('cancel', new FunctionObject($this, 'cancel'));
    }

    function initialize() {
    	$input =& new Input($this->text);
    	//$input->view->setAttribute('hidden', 'true');
    	$this->addComponent($input, 'prompt_input');
    }

    function stop() {
    	$this->prompt_input->delete();
    }



    function accept() {
    	$this->callback();
    	$this->on_accept->callWith($this->text);
    }

    function cancel() {
    	$this->callback();
    	$this->on_cancel->call();
    }

    function onAccept(&$function) {
		$this->on_accept=&$function;
    }

    function onCancel(&$function) {
		$this->on_cancel=&$function;
    }
}

class ModalErrorDialog extends AjaxComponent {
	var $message;

    function ModalErrorDialog($message) {
    	$this->message = $message;
    	parent::AjaxComponent();
    }


	function start() {
    	$app =& Window::getActiveInstance();
    	$app->addAjaxCommand(new AjaxCommand('openErrorDialog', array($this->renderMessage(), $this->getId(), constant('pwb_url'))));
    	$this->addInterestIn('accept', new FunctionObject($this, 'accept'));
    }

    function accept() {
    	$this->callback();
    	$this->on_accept->call();
    }

    function onAccept(&$function) {
		$this->on_accept=&$function;
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
    	$app =&  Window::getActiveInstance();
    	$app->addAjaxCommand(new AjaxCommand('openQuestionDialog', array($this->renderMessage(), $this->getId(), constant('pwb_url'))));
    	$this->addInterestIn('yes', new FunctionObject($this, 'yes'));
    	$this->addInterestIn('no', new FunctionObject($this, 'no'));
    }

    function yes() {
    	//var_dump("yes");
    	$this->callback();
    	$this->on_yes->call();
    }

    function no() {
    	$this->callback();
    	$this->on_no->call();
    }

    function onYes(&$function) {
		$this->on_yes=&$function;
    }

    function onNo(&$function) {
		$this->on_no=&$function;
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
    	$app =&  Window::getActiveInstance();
    	$app->addAjaxCommand(new AjaxCommand('openQuestionDialog', array('message' => $this->renderMessage(), 'callback_comp' => $this->getId())));
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

	function AjaxCommand($command, $params=array()) {
		$this->command = $command;
		$this->params = $params;
	}

	function renderAjaxResponseCommand() {
		$xml =  '<call>';
		$xml .= $this->renderStdResponseCommand();
		$xml .= '</call>';
		return $xml;
	}
	function renderStdResponseCommand() {

		if (count($this->params)==0) {
			if (substr($this->command,-1)==')' || substr($this->command,-2,-1)==')') {
				return $this->command;
			} else {
				return $this->command.'();';
			}
		}
		$xml =  $this->command . '("'.
			implode('","',$this->params) . '");';

		return $xml;
	}
}

?>