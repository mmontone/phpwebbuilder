<?php

require_once dirname(__FILE__) . '/../Component.class.php';

class QuestionDialog extends Component
{
	var $question;

	function QuestionDialog($question, $callback_actions=array('on_yes'=>'question_accepted', 'on_no' => 'question_cancelled')) {
		$this->question = $question;
		parent::Component($callback_actions);
	}

	function declare_actions() {
		return array('yes', 'no');
	}
	function initialize(){
		/*$this->add_component(new Text("<h1>"));
		$this->add_component(new Text($this->question), "question");
		$this->add_component(new Text("</h1><br />"));
		$this->add_component(new ActionLink($this, 'yes', 'Yes'),"yes");
		$this->add_component(new Text("<br />"));*/
		$this->add_component(new ActionLink($this, 'no', 'No'), "no");
	}

	function yes() {
		$this->callback('on_yes');
	}

	function no() {
		$this->callback('on_no');
	}
}
?>