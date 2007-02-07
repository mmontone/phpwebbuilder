<?
class User extends PersistentObject {
	var $permissions = array ();
	function initialize() {
		$this->table = "users";
		$this->addField(new TextField(array('fieldName'=>'user', 'is_index'=>TRUE)));
		$this->addField(new PasswordField(array('fieldName'=>'pass')));
		$this->addField(new CollectionField(
			array('reverseField'=>'user',
				'type' => 'UserRole',
				'display' => 'Roles'
		)));
		/*$this->addField(new CollectionField(
			array(
				'type' => 'Role',
				'display' => 'Roles',
				'joinTable' => 'UserRole',
				'fieldName' => 'roles',
				'joinFieldOwn'=>'user',
				'joinField'=>'role'
		)));*/
	}

	function & loadUser($user, $pass) {
		$u =& User::getWithIndex('User',array("user"=>"'$user'",
								"pass"=>"'$pass'"));
		if ($u!=null) {
			$u->getPermissions();
			return $u;
		} else
			$f = false;
			return $f;
	}


	function getUserId() {
		return $this->getIdOfClass('User');
	}

	function hasRole($uid, $roleid) {
		$urc = new PersistentCollection(UserRole);
		$urc->setCondition("user", "=",	$uid);
		$urc->setCondition("role", "=", $roleid);
		return count($urc->elements()) > 0;
	}
	function & login($user, $pass) {
		$usr = & User :: loadUser($user, $pass);

		if ($usr) {
			$uid=$usr->getUserId();
			Session::setAttribute("id",$uid);
			Session::setAttribute("Username",$usr->user->getValue());
			Session::setAttribute("User", $usr);
		}

		return $usr;
	}

	function logout() {
		Session::removeAttribute("User");
	}

	function & logged() {
		if (Session::getAttribute("User")===null) {
			$usr =& User :: login('guest', 'guest');

			if (!$usr) {
				$usr = & new User();
				$usr->user->setValue('guest');
				Session::setAttribute("id",$usr->getUserId());
				Session::setAttribute("Username",$usr->user->getValue());
				Session::setAttribute("User",$usr);
			}
		}

		return Session::getAttribute("User");
	}

	function getPermissions() {
		$id = $this->getUserId();
		$r =& #@select RolePermission (permission as permission) from p: RolePermission, u:UserRole where p.role =u.role AND u.user=$id@#;
		$id;
		foreach ($r->elements() as $p) {
			$this->permissions[] = strtolower($p->permission->getValue());
		}
	}
	function hasPermission($permission) {
		return in_array(strtolower($permission), $this->permissions);
	}
	function hasPermissions($permissions) {
		$ok = false;
		foreach ($permissions as $p) {
			$ok |= $this->hasPermission($p);
		}
		return $ok;
	}
	function is_guest() {
		$u = & User :: logged();
		return $u->user->getValue() == 'guest';
	}
}
?>
