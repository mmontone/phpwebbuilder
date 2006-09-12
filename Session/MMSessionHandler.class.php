<?php

define('MAX_SIZE',32000000);
define('MAX_SIZE_B',strlen(MAX_SIZE));

if (!class_exists('SessionHandler')) {
	class SessionHandler {}
}

class MMSessionHandler extends SessionHandler {
	var $key, $id, $name, $path;
	function open($path, $name){
		$this->name =$name;
		$this->path =$path;
		return true;
	}
	function close(){
		return $this->gc();
	}
	function read($sess_id){
		$file = $this->path.'/sess_'.$sess_id;
		fclose(fopen($file, 'w'));
		$this->key = ftok($file, 'q');
		$this->id = @shmop_open($this->key, 'w',0,0);
		if (!$this->id) {
			$this->id = shmop_open($this->key, 'c',0666,MAX_SIZE);
			return '';
		} else {
			$s = shmop_read($this->id, 0,  MAX_SIZE_B);
			$r = shmop_read($this->id, MAX_SIZE_B, $s);
			return $r;
		}
	}
	function write($sess_id, $data){
		shmop_write($this->id, strlen($data), 0);
		$bw = shmop_write($this->id, $data, MAX_SIZE_B);
		return $bw==strlen($data);
	}
	function destroy(){
		return shmop_delete($this->id);
	}
	function gc(){
		return true;
	}
	function setSessionHooks(){
		$session_class =& $this;
		session_set_save_handler(array(&$session_class, 'open'),
						 array(&$session_class, 'close'),
                         array(&$session_class, 'read'),
                         array(&$session_class, 'write'),
                         array(&$session_class, 'destroy'),
                         array(&$session_class, 'gc'));
	}
}
?>