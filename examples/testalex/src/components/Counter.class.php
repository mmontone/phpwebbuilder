<?php

class Counter extends Component
{
	var $counter;

	function Counter($value=0) {
		parent::Component();
		$this->counter = $value;
	}

	function render_on(&$html) {
		$html->text("<h1>" . $this->counter . "</h1></br>");
		$html->text("<a href=" . $this->render_action_link('question_incrementation') . ">Incrementar</a></br>\n");
		$html->text("<a href=" . $this->render_action_link('question_decrementation') . ">Decrementar</a></br>\n");
	}

	function declare_actions() {
		return array('question_incrementation', 'question_decrementation');

	}

	function question_incrementation() {
          $this->call (new QuestionDialog("¿Realmente quiere incrementar?", array('on_yes' => callback($this, 'notify_incrementation'),
                                                                                  'on_no' => callback($this, 'user_cancelled'))));
	}

	function question_decrementation() {
          $this->call (new QuestionDialog("¿Realmente quiere decrementar?", array('on_yes' => callback($this, 'notify_decrementation'),
                                                                                  'on_no' => callback($this, 'user_cancelled'))));
	}

	function notify_incrementation() {
          $this->call (new NotificationDialog('Voy a incrementar', array('on_accept' => callback($this, 'increment'))));
	}

	function notify_decrementation() {
          $this->call (new NotificationDialog('Voy a decrementar', array('on_accept' => callback($this, 'decrement'))));
	}

	function user_cancelled() {
	}

	function increment() {
		$this->counter = $this->counter + 1;
        $this->triggerEvent('model_changed');
	}

	function decrement() {
		$this->counter = $this->counter - 1;
        $this->triggerEvent('model_changed');
	}
}
?>