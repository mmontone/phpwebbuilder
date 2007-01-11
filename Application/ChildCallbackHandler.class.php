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
		if ($callback != null) {
			if ($callbackComponent->registered_callbacks[$callback] != null) {
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
}
?>