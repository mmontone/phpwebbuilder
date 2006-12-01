<?php

$d = dirname(__FILE__);

compile_once ($d.'/Base/Menu/MenuSection.class.php');
compile_once ($d.'/Base/Menu/MenuItem.class.php');
compile_once ($d.'/Base/Permissions/RolePermission.class.php');
compile_once ($d.'/Base/Permissions/User.class.php');
compile_once ($d.'/Base/Permissions/UserRole.class.php');
compile_once ($d.'/Base/Permissions/Role.class.php');
compile_once ($d.'/File.class.php');
compile_once ($d.'/DBInfo.class.php');
compile_once ($d.'/DBVersion.class.php');

/*
compile_once ($d.'/Translator.class.php');
compile_once ($d.'/Message.class.php');
compile_once ($d.'/MessageTranslation.class.php');
*/
?>