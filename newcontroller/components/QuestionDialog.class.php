<?php

require_once dirname(__FILE__) . '/../Component.class.php';

class QuestionDialog extends Component
{
	var $question;

	function QuestionDialog($question, $callback_actions=array('on_yes'=>'question_accepted', 'on_no' => 'question_cancelled')) {
		parent::Component($callback_actions);
		$this->question = $question;
	}

	function declare_actions() {
		return array('yes', 'no');
	}

	function render_on(&$html) {
		$html->text("<h1>" . $this->question . "</h1></br>\n");
		$html->begin_form();
		$html->submit_button(array('label' => 'Yes', 'action' => 'yes'));
		$html->submit_button(array('label' => 'No', 'action' => 'no'));
		$html->text("</form>\n");
	}

	function yes() {
		$this->callback('on_yes');
	}

	function no() {
		$this->callback('on_no');
	}
}
?>