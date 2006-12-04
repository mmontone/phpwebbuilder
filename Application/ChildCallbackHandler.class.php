<?php

class ChildCallbackHandler {
    function takeControlOf(&$callbackComponent, $callback, &$parameters) {
		if ($callback != null) {
			if ($callbackComponent->registered_callbacks[$callback] != null) {
				$callbackComponent->registered_callbacks[$callback]->callWith($parameters);
			}
		}
	}

	function dynTakeControlOf(&$callbackComponent, $callback, &$parameters) {
		if ($callback != null) {
			if ($callbackComponent->registered_callbacks[$callback] != null) {
				$callbackComponent->registered_callbacks[$callback]->callWith($parameters);
			}
			else {
				$p =& $callbackComponent->getParent();
				if ($p == null) {
					print_backtrace_and_exit('No parent');
				}
				else {
					$p->dynCallbackWith($callback, $parameters);
				}
			}
		}
	}

	function releaseAll() {

	}
}
?>