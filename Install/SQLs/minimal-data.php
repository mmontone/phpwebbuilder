<?

/*
$basesql = "INSERT IGNORE INTO  `".baseprefix."MenuSection` VALUES (1, 0, 'Database Structure', 0);
INSERT IGNORE INTO `".baseprefix."MenuSection` VALUES (2, 0, 'User Admin', 0);

INSERT IGNORE INTO `".baseprefix."MenuItem` VALUES (1, 0, 'Database Administration', 'DBController', '', 1);
INSERT IGNORE INTO `".baseprefix."MenuItem` VALUES (2, 0, 'Roles Administration', 'RolesController', '', 2);


INSERT IGNORE INTO `".baseprefix."Role` VALUES (1, 0, 'Superuser', '');
INSERT IGNORE INTO `".baseprefix."Role` VALUES (2, 0, 'Guest', '');

INSERT IGNORE INTO `".baseprefix."RolePermission` VALUES (1, 0, 1, 'DatabaseAdmin');
INSERT IGNORE INTO `".baseprefix."RolePermission` VALUES (2, 0, 1, '*');
INSERT IGNORE INTO `".baseprefix."RolePermission` VALUES (3, 0, 1, 'UserAdmin');
-- INSERT IGNORE INTO `".baseprefix."RolePermission` VALUES (4, 0, 1, 'DatabaseAdmin');

INSERT IGNORE INTO `".baseprefix."UserRole` VALUES (1, 0, 1, 1);
INSERT IGNORE INTO `".baseprefix."UserRole` VALUES (2, 0, 2, 2);

INSERT IGNORE INTO `".baseprefix."users` VALUES (1, 0, 'admin', 'PWB-admin');
INSERT IGNORE INTO `".baseprefix."users` VALUES (2, 0, 'guest', 'guest');"*/

$db =& DBSession::Instance();
$db->beginTransaction();

$db_section =& new MenuSection;
$db_section->name->setValue('Database structure');
$db_section->menuorder->setValue(0);

$db_admin =& new MenuItem;
$db_admin->name->setValue('Database administration');
$db_admin->controller->setValue('DBController');
$db_admin->section->setTarget($db_section);

if (!$db->save($db_section)) {
	handle_dberror($db);
}

if (!$db->save($db_admin)) {
	handle_dberror($db);
}

$user_admin =& new MenuSection;
$user_admin->name->setValue('User admin');
$user_admin->menuorder->setValue(0);

$roles_admin =& new MenuItem;
$roles_admin->name->setValue('Roles administration');
$roles_admin->controller->setValue('RolesController');
$roles_admin->section->setTarget($user_admin);

if (!$db->save($user_admin)) {
	handle_dberror($db);
}

if (!$db->save($roles_admin)) {
	handle_dberror($db);
}

$su_role =& new Role;
$su_role->name->setValue('Superuser');
if (!$db->save($su_role)) {
	handle_dberror($db);
}

$guest_role =& new Role;
$guest_role->name->setValue('Guest');
if (!$db->save($guest_role)) {
	handle_dberror($db);
}

$dbadmin_p =& new RolePermission;
$dbadmin_p->role->setTarget($su_role);
$dbadmin_p->permission->setValue('DatabaseAdmin');
if (!$db->save($dbadmin_p)) {
	handle_dberror($db);
}

$all_p =& new RolePermission;
$all_p->role->setTarget($su_role);
$all_p->permission->setValue('*');
if (!$db->save($all_p)) {
	handle_dberror($db);
}

$useradmin_p =& new RolePermission;
$useradmin_p->role->setTarget($su_role);
$useradmin_p->permission->setValue('UserAdmin');
if (!$db->save($useradmin_p)) {
	handle_dberror($db);
}

$admin =& new User;
$admin->user->setValue('admin');
$admin->pass->setValue('PWB-admin');
if (!$db->save($admin)) {
	handle_dberror($db);
}

$admin_r =& new UserRole;
$admin_r->user->setTarget($admin);
$admin_r->role->setTarget($su_role);
if (!$db->save($admin_r)) {
	handle_dberror($db);
}

$guest =& new User;
$guest->user->setValue('guest');
$guest->pass->setValue('guest');
if (!$db->save($guest)) {
	handle_dberror($db);
}

$guest_r =& new UserRole;
$guest_r->user->setTarget($guest);
$guest_r->role->setTarget($guest_role);
if (!$db->save($guest_r)) {
	handle_dberror($db);
}

$version =& new DBVersion;
$version->version->setValue(1);
if (!$db->save($version)) {
	handle_dberror($db);
}

$db->commit();

?>