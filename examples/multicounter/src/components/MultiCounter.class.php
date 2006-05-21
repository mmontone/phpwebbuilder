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
            $this->addComponent($counter, $i);
		}
	}

	function render_on(&$html) {
		for ($i = 0; $i < $this->count; $i++) {
			$counter =& $this->componentAt($i);
			$counter->renderContent($html);
			$html->text("</br></br>");
		}
	}
}

?>