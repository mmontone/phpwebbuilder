<?
$basesql = "INSERT IGNORE INTO  `".baseprefix."MenuSection` VALUES (1, 0, 'Database Structure', 0);
INSERT IGNORE INTO `".baseprefix."MenuSection` VALUES (2, 0, 'User Admin', 0);

INSERT IGNORE INTO `".baseprefix."MenuItem` VALUES (1, 0, 'Database Administration', 'DBController', '', 1);
INSERT IGNORE INTO `".baseprefix."MenuItem` VALUES (2, 0, 'Roles Administration', 'RolesController', '', 2);


INSERT IGNORE INTO `".baseprefix."Role` VALUES (1, 0, 'Superuser', '');
INSERT IGNORE INTO `".baseprefix."Role` VALUES (2, 0, 'Guest', '');

INSERT IGNORE INTO `".baseprefix."RolePermission` VALUES (1, 0, 1, 'DatabaseAdmin');
INSERT IGNORE INTO `".baseprefix."RolePermission` VALUES (2, 0, 1, '*');
INSERT IGNORE INTO `".baseprefix."RolePermission` VALUES (3, 0, 1, 'UserAdmin');
INSERT IGNORE INTO `".baseprefix."RolePermission` VALUES (4, 0, 1, 'DatabaseAdmin');

INSERT IGNORE INTO `".baseprefix."UserRole` VALUES (1, 0, 1, 1);
INSERT IGNORE INTO `".baseprefix."UserRole` VALUES (2, 0, 2, 2);

INSERT IGNORE INTO `".baseprefix."users` VALUES (1, 0, 'admin', 'PWB-admin');
INSERT IGNORE INTO `".baseprefix."users` VALUES (2, 0, 'guest', 'guest');"
?>