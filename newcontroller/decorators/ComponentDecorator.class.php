<?php

class ComponentDecorator extends ComponentHolder  {

    var $target;

    function ComponentDecorator(&$component) {
    	$this->decorate($component);
    }

	function decorate(&$target) {
        $this->target = $target;
		$target->add_decorator($this);
    }

    function renderAll(&$out) {
        $this->render_on(&$out);
    }

    function render_on(&$out) {}
}

?>