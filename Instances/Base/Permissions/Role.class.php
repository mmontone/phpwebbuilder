<?

class Role extends PersistentObject {
    function initialize () {
         $this->table = "Role";
         $this->addField(new textField("name", TRUE));
         $this->addField(new textArea("description", FALSE));
         $this->addField(new CollectionField('role',array('type'=>'RolePermission','display'=>'Permissions')));
    }

	function userHasPermission($id, $permission){
		if ($id == null) return false;
        $db =& DBSession::Instance();
		if (is_array($permission)) {
			$perm ='(';
			foreach($permission as $p) {
				$perm .= ' p.permission=\''.$p.'\' OR ';
			}
			$perm .= ' 1=0)';
		} else $perm = ' p.permission=\''.$permission.'\' ';
		/*Necesito relacionar UserRole con RolePermission*/
		$sql = implode(array('SELECT * FROM ',baseprefix,'UserRole u, ',baseprefix,'RolePermission p',
				' WHERE user =', $id,
				' AND ',$perm,
				' AND u.role=p.role'));
		$reg=& $db->SQLExec($sql, FALSE, $n=0, $rows=0);
		unset($sql);
		$can = mysql_num_rows ($reg) > 0;
		unset($reg);
    	return $can;
	}
	/*function userHasPermission($id, $permission){
		if ($id == null) return false;
        $db =& new MySQLdb;
		if (is_array($permission)) {
			$perm ='(';
			foreach($permission as $p) {
				$perm .= ' p.permission=\''.$p.'\' OR ';
			}
			$perm .= ' 1=0)';
		} else $perm = ' p.permission=\''.$permission.'\' ';
		$sql = implode(array('SELECT * FROM ',baseprefix,'UserRole u, ',baseprefix,'RolePermission p',
				' WHERE user =', $id,
				' AND ',$perm,
				' AND u.role=p.role'));
		$reg=& $db->SQLExec($sql, FALSE, 0, $rows=0);
		unset($sql);
		$can = mysql_num_rows ($reg) > 0;
		unset($reg);
    	return $can;
	}*/

	function havePermission ($permission) {
			$db = DBSession::Instance();
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
					" AND " . $this->id->getValue() . "=p.role";
			$reg=$db->SQLExec($sql, FALSE, $n=null, $rows=0);
	    	return ((mysql_num_rows ($reg) > 0));
	}
}
?>
