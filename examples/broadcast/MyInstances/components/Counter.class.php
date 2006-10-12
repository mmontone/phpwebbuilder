<?php

class Counter extends Component
{
	var $counter;

	function Counter($value=0) {
		$this->counter = $value;
		parent::Component();
	}

	function initialize() {
		/*$html->text("<h1>" . $this->counter . "</h1></br>");
		$html->text("<a href=" . $this->render_action_link('question_incrementation') . ">Incrementar</a></br>\n");
		$html->text("<a href=" . $this->render_action_link('question_decrementation') . ">Decrementar</a></br>\n");*/
		$this->setValue($this->counter);
		$this->addComponent(new ActionLink($this,'question_incrementation', 'Increment', $n=null), 'inc');
		$this->addComponent(new ActionLink($this,'question_decrementation', 'Decrement', $n=null), 'dec');
	}
	function setValue($val){
		$this->counter = $val;
		$this->addComponent(new Label($this->counter), 'count');
	}
	function question_incrementation() {
          $this->call ($qd =& QuestionDialog("Do you really want to increment?"));
          $qd->registerCallbacks(array('on_yes' => callback($this, 'notify_incrementation'),
                                                                                  'on_no' => callback($this, 'user_cancelled')));
	}
	function question_decrementation() {
          $this->call ($qd =& QuestionDialog("Do you really want to decrement?"));
          $qd->registerCallbacks(array('on_yes' => callback($this, 'notify_decrementation'),
                                                                                  'on_no' => callback($this, 'user_cancelled')));
	}
	function notify_incrementation() {
          $this->call ($nd =& NotificationDialog::create('Going to increment'));
          $nd->registerCallbacks(array('on_accept' => callback($this, 'increment')));
	}
	function notify_decrementation() {
          $this->call ($nd =& NotificationDialog::create('Going to decrement'));
          $nd->registerCallbacks(array('on_accept' => callback($this, 'decrement')));
	}
	function user_cancelled() {
	}
	function increment() {
		$this->setValue($this->counter + 1);
        $this->triggerEvent('model_changed', $n=null);
	}
	function decrement() {
		$this->setValue($this->counter - 1);
        $this->triggerEvent('model_changed', $n=null);
	}
}
?>