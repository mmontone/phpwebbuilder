<?php

require_once dirname(__FILE__) . '/Counter.class.php';

class MultiCounter extends Component
{
	var $count;

	function MultiCounter($count=3) {
		parent::Component();
		$this->count = $count;
		for ($i = 0; $i < $count; $i++) {
            $counter =& new Counter(0);
            $counter->addEventListener(array('model_changed'=>'updateCounters'), $this);
			$this->addComponent($counter, $i);
		}
	}

	function declare_actions() {
          return array();
        }
          

        function render_on(&$html) {
		for ($i = 0; $i < $this->count; $i++) {
			$counter =& $this->componentAt($i);
			$counter->renderContent($html);
			$html->text("</br></br>");
		}
	}

    function updateCounters(&$signaler) {
        for ($i = 0; $i < $this->count; $i++) {
            $counter =& $this->componentAt($i);
            $counter->counter = $signaler->counter;
        }
    }
}

?>