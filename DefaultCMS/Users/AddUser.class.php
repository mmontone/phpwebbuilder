<?php

require_once dirname(__FILE__).'/../CMS/EditObjectComponent.class.php';

class AddUser extends Component {
	function initialize(){
		$this->addComponent(new Input(new ValueHolder($u="")),'username');
		$this->addComponent(new Input(new ValueHolder($p1="")),'password');
		$this->addComponent(new Input(new ValueHolder($p2="")),'password_confirm');
		$this->addComponent(new Text(new ValueHolder($s="")),'status');
       	$this->addComponent(new ActionLink($this, 'save', 'register', $n=null), 'save');
       	$this->addComponent(new ActionLink($this, 'callback', 'cancel', $n), 'cancel');
	}
    function save(){
    	$p1 = $this->password->getValue();
    	$p2 = $this->password_confirm->getValue();
    	$un = $this->username->getValue();
		if ($un == ""){
			$this->status->setValue('username is blank');
			return;
    	}
    	if ($p1 == ""){
			$this->status->setValue('password is blank');
			return;
    	}
    	if ($p1 != $p2){
			$this->status->setValue('passwords do not match');
			return;
    	}
		$ok = $this->createUser($un, $p1);
		if ($ok){
			$this->status->setValue('registration successful');
		}else{
			$this->status->setValue('registration failed, try another username');
		}
    }
    function createUser($un, $p){
    	$u =& new User;
    	$u->user->setValue($un);
   		$u->pass->setValue($p);
   		return $u->save();
    }
}

?>