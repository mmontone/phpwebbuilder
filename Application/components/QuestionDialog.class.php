<?php

class QuestionDialog extends Component
{
	var $message;
    var $button_pressed = false;

	function QuestionDialog($question) {
		$this->message = $question;
		parent::Component();
	}
	function &create($message){
		if (Application::rendersAjax()) {
			$qd =&new ModalQuestionDialog($message);
		} else {
			$qd =&new QuestionDialog($message);
		}
		return $qd;
	}
	function initialize(){
		$this->addComponent(new Label($this->message), 'question');
		$this->addComponent(new CommandLink(array('proceedFunction' => new FunctionObject($this, 'yes'),
		                                          'text' => 'Yes'),'yes'), 'yes');
		$this->addComponent(new CommandLink(array('proceedFunction' => new FunctionObject($this, 'no'), 'text' => 'No'), 'no'),'no');
	}

	function yes() {
		$this->button_pressed = true;
        $this->callback('on_yes');
	}

	function no() {
		$this->button_pressed = true;
        $this->callback('on_no');
	}

	function onYes(&$function) {
		$this->registerCallback('on_yes', $function);
	}

	function onNo(&$function) {
		$this->registerCallback('on_no', $function);
	}
}

?>