<?

class User extends PersistentObject {
    function initialize () {
         $this->table = "users";
         $this->addField(new textField("user", TRUE));
         $this->addField(new passwordField("pass", FALSE));
         $this->addField(new CollectionField("user", array('type'=>'UserRole','display'=>'Roles')));
    }
	function validate(&$error_msgs) {
	  	return $this->checkNotEmpty(array("user"), &$error_msgs);
	  }
	function &loadUser($user,$pass){
	$db = new MySQLdb;
	$col = new PersistentCollection(User);
	$col->conditions["user"] = array("=", "'".$user."'");
	$col->conditions["pass"] = array("=", "'".$pass."'");
	$objs = $col->objects();
	if (count($objs)>0) {
		$newusr =& User::getWithId('User', $objs[0]->id->value);
  		return $newusr;
	}else
  		return FALSE;
    }
	function getUserId(){
		return $this->getIdOfClass("User");
	}
    function hasRole($uid, $roleid){
    	$urc = new PersistentCollection(UserRole);
    	$urc->conditions["user"] = array("=", $uid);
    	$urc->conditions["role"] = array("=", $roleid);
    	return count($urc->objects())>0;
    }
	function &login ($user, $pass) {
	$usr =& User::loadUser($user, $pass);
	  if ($usr) {
	    $_SESSION[sitename]["id"] = $usr->getUserId();
    	$_SESSION[sitename]["Username"] = $usr->user->value;
		$_SESSION[sitename]["User"] =& $usr;
  	  }
  	  return $usr;
}

}
?>
