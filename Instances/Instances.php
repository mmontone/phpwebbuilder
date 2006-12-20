<?php

$d = dirname(__FILE__);

compile_once (dirname(__FILE__).'/Base/Menu/MenuSection.class.php');
compile_once (dirname(__FILE__).'/Base/Menu/MenuItem.class.php');
compile_once (dirname(__FILE__).'/Base/Permissions/RolePermission.class.php');
compile_once (dirname(__FILE__).'/Base/Permissions/User.class.php');
compile_once (dirname(__FILE__).'/Base/Permissions/UserRole.class.php');
compile_once (dirname(__FILE__).'/Base/Permissions/Role.class.php');
compile_once (dirname(__FILE__).'/File.class.php');
compile_once (dirname(__FILE__).'/DBInfo.class.php');
compile_once (dirname(__FILE__).'/DBVersion.class.php');

/*
compile_once (dirname(__FILE__).'/Translator.class.php');
compile_once (dirname(__FILE__).'/Message.class.php');
compile_once (dirname(__FILE__).'/MessageTranslation.class.php');
*/
?>