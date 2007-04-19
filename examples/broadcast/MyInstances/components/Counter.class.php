<?php

class Counter extends Component
{
	var $counter;

	function Counter($value=0) {
		$this->counter = new ValueHolder($value);
		parent::Component();
	}

	function initialize() {
		$this->addComponent(new Text($this->counter), 'count');
		$this->addComponent(new ActionLink($this,'question_incrementation', 'Increment', $n=null), 'inc');
		$this->addComponent(new ActionLink($this,'question_decrementation', 'Decrement', $n=null), 'dec');
	}
	function setValue($val){
		$this->counter->setValue($val);
	}
	function question_incrementation() {
          $this->call ($qd =& QuestionDialog::create("Do you really want to increment?"));
          $qd->registerCallbacks(array(
				'on_yes' => new FunctionObject($this, 'notify_incrementation'),
				'on_no' => new FunctionObject($this, 'user_cancelled')
			));
	}
	function question_decrementation() {
          $this->call ($qd =& QuestionDialog::create("Do you really want to decrement?"));
          $qd->registerCallbacks(array(
				'on_yes' => new FunctionObject($this, 'notify_decrementation'),
				'on_no' => new FunctionObject($this, 'user_cancelled')
			));
	}
	function notify_incrementation() {
          $this->call ($nd =& NotificationDialog::create('Going to increment'));
          $nd->registerCallback('on_accept', new FunctionObject($this, 'increment'));
	}
	function notify_decrementation() {
          $this->call ($nd =& NotificationDialog::create('Going to decrement'));
          $nd->registerCallback('on_accept',new FunctionObject($this, 'decrement'));
	}
	function user_cancelled() {
	}
	function increment() {
		$this->setValue($this->counter->getValue() + 1);
        $this->triggerEvent('model_changed', $n=null);
	}
	function decrement() {
		$this->setValue($this->counter->getValue() - 1);
        $this->triggerEvent('model_changed', $n=null);
	}
}
?>