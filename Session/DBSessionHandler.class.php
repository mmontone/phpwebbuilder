<?php
class DBSessionHandler extends SessionHandler{
	var $instance;
	var $ss;
	function open($path, $name){
		$this->ss =& new PersistentCollection('Session');
		$this->ss->conditions['session_name']=array('=',"'$name'");
		$this->instance =& new Session;
		$this->instance->session_name->setValue($name);
		return true;
	}
	function close(){
		return $this->gc();
	}
	function read($sess_id){
		$this->ss->conditions['session_id']=array('=',"'$sess_id'");
		if (!$this->ss->isEmpty()){
			$this->instance =& $this->ss->first();
			$ret = $this->instance->session_data->getValue();
			return $ret;
		} else {
			return '';
		}
	}
	function write($sess_id, $data){
		$i =& $this->instance;
		$i->session_data->setValue(str_replace('\'','\\\'',$data));
		$i->session_id->setValue($sess_id);
		$i->last_updated->setValue($i->last_updated->now());
		$ok = $i->save();
		if (!$ok) print_backtrace('error saving session '.DB::lastError());
		return true;
	}
	function destroy(){
		return $this->instance->delete();
	}
	function gc(){
		$db =& DB::instance();
		$q = 'DELETE FROM '.$this->instance->tableName().' WHERE last_updated < ADDTIME(now(), \'-00:20:00.0\')';
		$db->query($q);
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