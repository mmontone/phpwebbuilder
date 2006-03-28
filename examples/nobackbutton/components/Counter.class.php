<?php

class Counter extends Component
{
	var $counter;

	function Counter($value=0) {
		parent::Component();
		$this->counter = $value;
	}

	function render_on(&$out) {
		$out .= "<h1>" . $this->counter . "</h1></br>\n";
		$out .= "<a href=" . $this->render_action_link('question_incrementation') . ">Incrementar</a></br>\n";
		$out .= "<a href=" . $this->render_action_link('question_decrementation') . ">Decrementar</a></br>\n";
	}

	function declare_actions() {
		return array('question_incrementation', 'question_decrementation');

	}

	function question_incrementation() {
		$this->call (new QuestionDialog("¿Realmente quiere incrementar?", array('on_yes' => 'notify_incrementation',
		                                                                        'on_no' => 'user_cancelled')));
	}

	function question_decrementation() {
		$this->call (new QuestionDialog("¿Realmente quiere decrementar?", array('on_yes' => 'notify_decrementation',
		                                                                        'on_no' => 'user_cancelled')));
	}

	function notify_incrementation() {
		$this->call (new NotificationDialog('Voy a incrementar', array('on_accept' => 'increment')));
	}

	function notify_decrementation() {
		$this->call (new NotificationDialog('Voy a decrementar', array('on_accept' => 'decrement')));
	}

	function user_cancelled() {
        $this->render();
	}

	function increment() {
		$this->counter = $this->counter + 1;
        $this->trigger_event('model_changed');
	}

	function decrement() {
		$this->counter = $this->counter - 1;
        $this->triggerEvent('model_changed');
	}
}
?>