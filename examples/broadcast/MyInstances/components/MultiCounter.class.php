<?php

class MultiCounter extends Component
{
	var $count;
	var $counters = array();
	function MultiCounter($count=3) {
		$this->count = $count;
		parent::Component();
	}
	function initialize(){
		for ($i = 0; $i < $this->count; $i++) {
            $counter =& new Counter(0);
            $counter->addEventListener(array('model_changed'=>'updateCounters'), $this);
            $this->counters[$i] =& $counter;
			$this->addComponent($counter, $i);
		}
	}

    function updateCounters(&$signaler) {
        for ($i = 0; $i < $this->count; $i++) {
            $counter =& $this->counters[$i];
            $counter->setValue($signaler->counter);
        }
    }
}

?>