<?php

class APCSessionHandler extends SessionHandler {
	var $id, $name, $path, $sess_id;
	function open($path, $name) {
		return true;
	}
	function close() {
		return $this->gc();
	}
	function read($sess_id) {
		$this->id = $sess_id;
        return (string) apc_fetch( 'sess_'.$sess_id);
	}
	function write($sess_id, $data) {
        return apc_store('sess_'.$sess_id, $data, ini_get("session.gc_maxlifetime"));
	}
	function destroy() {
		return apc_delete( 'sess_'.$this->id);
	}
	function gc() {
		return true;
	}
	function setSessionHooks() {
		$session_class = & $this;
		session_set_save_handler(array (
			& $session_class,
			'open'
		), array (
			& $session_class,
			'close'
		), array (
			& $session_class,
			'read'
		), array (
			& $session_class,
			'write'
		), array (
			& $session_class,
			'destroy'
		), array (
			& $session_class,
			'gc'
		));
	}
}
?>