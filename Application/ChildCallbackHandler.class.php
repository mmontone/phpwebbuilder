<?php

class ChildCallbackHandler {
    function takeControlOf(&$callbackComponent, $callback, &$parameters) {
		if ($callback != null) {
			if ($callbackComponent->registered_callbacks[$callback] != null) {
				$callbackComponent->registered_callbacks[$callback]->callWith($parameters);
			}
		}
	}

	function releaseAll() {

	}
}
?>