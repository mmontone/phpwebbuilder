<?php

class QuestionDialog extends Component
{
	var $question;

	function QuestionDialog($question, $callback_actions=array('on_yes'=>'question_accepted', 'on_no' => 'question_cancelled')) {
		$this->question = $question;
		parent::Component();
		$this->registerCallbacks($callback_actions);
	}

	function initialize(){
		$this->addComponent(new Label($this->question), 'question');
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
}
?>