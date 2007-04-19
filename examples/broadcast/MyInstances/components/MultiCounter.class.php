<?php

class MultiCounter extends Component
{
	var $count;
	function MultiCounter($count=3) {
		$this->count = $count;
		parent::Component();
	}
	function initialize(){
		for ($i = 1; $i <= $this->count; $i++) {
            $counter =& new Counter($i);
            $counter->addInterestIn('model_changed', new FunctionObject($this,'updateCounters'));
			$this->addComponent($counter, 'counter_'.$i);
		}
		$this->addComponent(new CheckBox($n=null), 'broadcast');
	}

    function updateCounters(&$signaler) {
        if ($this->broadcast->getValue()){
	        for ($i = 1; $i <= $this->count; $i++) {
	            $counter =& $this->{'counter_'.$i};
	            $counter->setValue($signaler->counter->getValue());
	        }
        }
    }
}

?>