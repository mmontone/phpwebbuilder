<?php

class ChildCallbackHandler {
    function takeControlOf(&$callbackComponent, $callback, &$parameters) {
		if ($callback != null) {
			if ($callbackComponent->registered_callbacks[$callback] != null) {
				$callbackComponent->registered_callbacks[$callback]->executeWith($parameters);
			}
		}
	}

	function dynTakeControlOf(&$callbackComponent, $callback, &$parameters) {
		#@calling_echo echo $this->printString() . ' taking control of: ' . $callbackComponent->printString() . 'callback: ' . $callback . '<br/>';@#
        if ($callback != null) {
			if ($callbackComponent->registered_callbacks[$callback] != null) {
				#@calling_echo echo $this->printString() . ' executing callback: ' . $callback . ' function: ' . $callbackComponent->registered_callbacks[$callback]->printString() .'<br/>';@#
                $callbackComponent->registered_callbacks[$callback]->executeWith($parameters);
			}
			else {
				$p =& $callbackComponent->getParent();
				#@typecheck $p:Component@#
				$p->dynCallbackWith($callback, $parameters);
			}
		}
	}

	function releaseAll() {

	}

	function informStopToCallers() {

	}

	function calleeStopped(&$callee) {

	}

	function debugPrintString() {
	  return print_object($this);
	}

	function printString() {
	  return $this->debugPrintString();
	}
}
?>