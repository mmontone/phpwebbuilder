<?php

class ChildCallbackHandler {
    function takeControlOf(&$callbackComponent, $callback=null, $parameters=array()) {
		if ($callback != null) {
			if ($callbackComponent->registered_callbacks[$callback] != null) {
				$callbackComponent->registered_callbacks[$callback]->call($parameters);
			}
		}
	}
}
?>