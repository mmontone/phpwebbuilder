<?

class Role extends PersistentObject {
    function initialize () {
         $this->table = "Role";
         $this->addField(new textField("name", TRUE));
         $this->addField(new textArea("description", FALSE));
         $this->addField(new CollectionField("role", "RolePermission"));
    }

	function userHasPermission($id, $permission){
		if ($id == null) $id = -1;
        $db = new MySQLdb;
		if (is_array($permission)) {
			$perm ="(";
			foreach($permission as $p) {
				$perm .= " p.permission='$p' OR ";
			}
			$perm .= " 1=0)";
		} else $perm = " p.permission='$permission' ";
		/*Necesito relacionar UserRole con RolePermission*/
		$sql = "SELECT * FROM ".baseprefix."UserRole u, ".baseprefix."RolePermission p".
				" WHERE user = $id".
				" AND $perm".
				" AND u.role=p.role";
		$reg=$db->SQLExec($sql, FALSE, 0);
		$can = ((mysql_num_rows ($reg) > 0));
		if (!$can) trigger_error("User $id can't ".print_r($permission, TRUE));
    	return $can;
	}
	function havePermission ($permission) {
			$db = new MySQLdb;
			if (is_array($permission)) {
				$perm ="(";
				foreach($permission as $p) {
					$perm .= " p.permission='$p' OR ";
				}
				$perm .= " 1=0)";
			} else $perm = " p.permission='$permission' ";
			/*Necesito relacionar UserRole con RolePermission*/
			$sql = "SELECT * FROM ".baseprefix."RolePermission p".
					" WHERE $perm".
					" AND ".$this->id->value."=p.role";
			$reg=$db->SQLExec($sql, FALSE, 0);
	    	return ((mysql_num_rows ($reg) > 0));
	}
	function canCheck () {
		$tc = new TableCheckView;
		$tc = $tc->viewFor($this);
		return ($tc->show()=="");
	}
}
?>
