<?php
class DBSessionHandler extends SessionHandler{
	var $instance;
	var $ss;
	var $isStarted = false;
	function open($path, $name){
		$this->ss =& new PersistentCollection('Session');
		$this->ss->setCondition('session_name','=',"'$name'");
		$this->instance =& new Session;
		$this->instance->session_name->setValue($name);
		return true;
	}
	function close(){
		$this->isStarted = false;
		return $this->gc();
	}
	function read($sess_id){
		$this->ss->setCondition('session_id','=',"'$sess_id'");
		if (!$this->ss->isEmpty()){
			$this->instance =& $this->ss->first();
			$ret = $this->instance->session_data->getValue();
			$this->isStarted = true;
			return $ret;
		} else {
			$this->isStarted = true;
			return '';
		}
	}
	function write($sess_id, $data){
		$i =& $this->instance;
		$i->session_data->setValue(addslashes($data));
		$i->session_id->setValue($sess_id);
		$i->last_updated->setNow();
		$ex =& $i->save();
		if (is_exception($ex)) print_backtrace('error saving session '.DBSession::lastError());
		return true;
	}
	function destroy(){
		return $this->instance->delete();
	}
	function gc(){
		$db =& DBSession::instance();
		$q = 'DELETE FROM '.$this->instance->tableName().' WHERE last_updated < ADDTIME(now(), \'-00:20:00.0\')';
		$db->query($q);
		return true;
	}
	function isStarted(){
		return $this->isStarted;
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