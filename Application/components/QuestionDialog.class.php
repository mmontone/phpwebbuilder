<?php

require_once dirname(__FILE__) . '/../Component.class.php';

class QuestionDialog extends Component
{
	var $question;
	// TODO: there should be no context. Make callbacks more general. FunctionObjects may be a good aproach
	var $context;

	function QuestionDialog($question, $callback_actions=array('on_yes'=>'question_accepted', 'on_no' => 'question_cancelled'), &$context) {
		$this->question = $question;
		$this->context =& $context;
		parent::Component($callback_actions);
	}

	function initialize(){
		$this->addComponent(new Label($this->question), 'question');
		$this->addComponent(new CommandLink(array('proceedFunction' => new FunctionObject($this, 'yes'),
		                                          'text' => 'Yes'),'yes'));
		$this->addComponent(new CommandLink(array('proceedFunction' => new FunctionObject($this, 'no'), 'text' => 'No'), 'no'));
	}

	function yes() {
		$this->callbackWith('on_yes', $this->context);
	}

	function no() {
		$this->callbackWith('on_no', $this->context);
	}
}
?>