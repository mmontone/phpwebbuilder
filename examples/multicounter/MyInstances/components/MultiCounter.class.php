<?php

require_once dirname(__FILE__) . '/Counter.class.php';

class MultiCounter extends Component
{
	var $count;
	function MultiCounter($count=3) {
		$this->count = $count;
		parent::Component();
	}
	function initialize(){
		for ($i = 0; $i < $this->count; $i++) {
            $counter =& new Counter(0);
			$this->addComponent($counter, $i);
		}
	}
}

?>