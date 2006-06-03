<?

class User extends PersistentObject {
	var $permissions = array();
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
	$db = DB::Instance();
	$col = new PersistentCollection(User);
	$col->conditions["user"] = array("=", "'".$user."'");
	$col->conditions["pass"] = array("=", "'".$pass."'");
	$objs = $col->objects();
	if (count($objs)>0) {
		$objs[0]->getPermissions();
  		return $objs[0];
	}else
  		return FALSE;
    }
	function getUserId(){
		return $this->getIdOfClass('User');
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
	function &logged(){
		return $_SESSION[sitename]["User"];
	}
	function getPermissions(){
        $db =& DB::Instance();
		$sql = implode(array('SELECT permission FROM ',baseprefix,'UserRole u, ',baseprefix,'RolePermission p',
				' WHERE user =', $this->getUserId(),
				' AND u.role=p.role'));
		 $ps = $db->queryDB($sql);
		 foreach($ps as $p){
		 	$this->permissions[]=$p['permission'];
		 }
	}
	function hasPermission($permission){
		return in_array($permission, $this->permissions);
	}
	function hasPermissions($permissions){
		$ok = false;
		foreach($permissions as $p){
			$ok |= $this->hasPermission($p);
		}
		return $ok;
	}
}
?>
