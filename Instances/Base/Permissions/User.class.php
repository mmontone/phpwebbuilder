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
	}
	function & loadUser($user, $pass) {
		$u = User::getWithIndex('User',array("user"=>"'$user'",
								"pass"=>"'$pass'"));
		if ($u!=null) {
			$u->getPermissions();
			return $u;
		} else
			return FALSE;
	}
	function getUserId() {
		return $this->getIdOfClass('User');
	}
	function hasRole($uid, $roleid) {
		$urc = new PersistentCollection(UserRole);
		$urc->conditions["user"] = array (
			"=",
			$uid
		);
		$urc->conditions["role"] = array (
			"=",
			$roleid
		);
		return count($urc->objects()) > 0;
	}
	function & login($user, $pass) {
		$usr = & User :: loadUser($user, $pass);

		if ($usr) {
			$_SESSION[sitename]["id"] = $usr->getUserId();
			$_SESSION[sitename]["Username"] = $usr->user->getValue();
			$_SESSION[sitename]["User"] = & $usr;
		}

		return $usr;
	}

	function & logged() {
		if (!isset ($_SESSION[sitename]["User"])) {
			$usr =& User :: login('guest', 'guest');

			if (!$usr) {
				$usr = & new User();
				$usr->user->setValue('guest');
				$_SESSION[sitename]["id"] = $usr->getUserId();
				$_SESSION[sitename]["Username"] = $usr->user->getValue();
				$_SESSION[sitename]["User"] = & $usr;
			}
		}

		return $_SESSION[sitename]["User"];
	}

	function getPermissions() {
		$db = & DB :: Instance();
		$sql = implode(array (
			'SELECT permission FROM ',
			baseprefix,
			'UserRole u, ',
			baseprefix,
			'RolePermission p',
			' WHERE user =',
		$this->getUserId(), ' AND u.role=p.role'));
		$ps = $db->queryDB($sql);
		foreach ($ps as $p) {
			$this->permissions[] = strtolower($p['permission']);
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
