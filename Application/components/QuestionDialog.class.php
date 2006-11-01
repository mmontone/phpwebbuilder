<?php

class QuestionDialog extends Component
{
	var $message;

	function QuestionDialog($question) {
		$this->message = $question;
		parent::Component();
	}
	function &create($message){
		if (constant('page_renderer') == 'AjaxPageRenderer') {
			return new ModalQuestionDialog($message);
		} else {
			return new QuestionDialog($message);
		}
	}
	function initialize(){
		$this->addComponent(new Label($this->message), 'question');
		$this->addComponent(new CommandLink(array('proceedFunction' => new FunctionObject($this, 'yes'),
		                                          'text' => 'Yes'),'yes'), 'yes');
		$this->addComponent(new CommandLink(array('proceedFunction' => new FunctionObject($this, 'no'), 'text' => 'No'), 'no'),'no');
	}

	function yes() {
		$this->callback('on_yes');
	}

	function no() {
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