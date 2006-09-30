<?php
define('MAX_SIZE', 32000000);
define('MAX_SIZE_B', strlen(MAX_SIZE));

if (!class_exists('SessionHandler')) {
	class SessionHandler {
	}
}

class MMSessionHandler extends SessionHandler {
	var $id, $name, $path, $sess_id;
	function open($path, $name) {
		$this->name = $name;
		$this->path = $path;
		return true;
	}
	function close() {
		return $this->gc();
	}
	function filename($sess_id) {
		return $this->path . '/sess_' . $sess_id;
	}
	function getFilename() {
		return $this->filename($this->sess_id);
	}
	function read($sess_id) {
		$this->sess_id = $sess_id;
		$file = $this->getFilename();
		touch($file);
		$key = ftok($file, 'q');
		$this->id = @ shmop_open($key, 'w', 0, 0);
		if (!$this->id) {
			$this->id = shmop_open($key, 'c', 0666, MAX_SIZE);
			return '';
		} else {
			$s = shmop_read($this->id, 0, MAX_SIZE_B);
			$r = shmop_read($this->id, MAX_SIZE_B, $s);
			return $r;
		}
	}
	function write($sess_id, $data) {
		shmop_write($this->id, strlen($data), 0);
		$bw = shmop_write($this->id, $data, MAX_SIZE_B);
		touch($this->getFilename());
		return $bw == strlen($data);
	}
	function destroy() {
		unlink($this->getFilename());
		return shmop_delete($this->id);
	}
	function gc() {
		$dh = opendir($this->path);
		while (($f = readdir($dh)) !== false) {
			$file = $this->path . '/' . $f;
			if ((filemtime($file) + (20 * 60)) < time()) {
				shmop_delete(shmop_open(ftok($file, 'q'), 'w', 0, 0));
				unlink($file);
			}
		}
		closedir($dh);
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