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
			return FALSE;
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
			$_SESSION["id"] = $usr->getUserId();
			$_SESSION["Username"] = $usr->user->getValue();
			$_SESSION["User"] = & $usr;
		}

		return $usr;
	}

	function logout() {
		unset($_SESSION["User"]);
	}

	function & logged() {
		if (!isset ($_SESSION["User"])) {
			$usr =& User :: login('guest', 'guest');

			if (!$usr) {
				$usr = & new User();
				$usr->user->setValue('guest');
				$_SESSION["id"] = $usr->getUserId();
				$_SESSION["Username"] = $usr->user->getValue();
				$_SESSION["User"] = & $usr;
			}
		}

		return $_SESSION["User"];
	}

	function getPermissions() {
		$r =& new Report();
		$r->defineVar('u','UserRole');
		$r->defineVar('p','RolePermission');

		$c1 =& new EqualCondition(array('exp1' => new AttrPathExpression('u', 'role'),
			                                'exp2' => new AttrPathExpression('p', 'role')));
		$c2 =& new EqualCondition(array('exp1' => new AttrPathExpression('u', 'user'),
			                                'exp2' => new ObjectExpression($this,'User')));

		$r->setPathCondition($c1);
		$r->setPathCondition($c2);
		$r->select('permission');
		foreach ($r->elements() as $p) {
			$this->permissions[] = strtolower($p->permission->getValue());
		}
		/*
		$db = & DBSession:: Instance();
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
		}*/
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
