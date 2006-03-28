<?php
require_once("pwb.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?= sitename ?> CMS</title>
<meta http-equiv="" content="text/html; charset=iso-8859-1">

<?
function addelement($obj, $text) {
  return "<li><a href=\"Action.php?ObjType=$obj&Action=List\" target=\"mainBack\">$text</a></li>";
}
?>

</head>

<body leftmargin="0">

<blockquote>
  <p><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Management</strong></font></p>
</blockquote>
  <p><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>User: <?echo $_SESSION[sitename]["Username"]; ?></strong></font></p>

<? 
	$menus = MenuSection::availableMenus();
	foreach ($menus as $m) {
		echo $m->showMenu();
	}

	$arr = get_subclasses("PersistentObject");
	echo "<ul>";
	foreach ($arr as $name){
		if (fHasPermission($_SESSION[sitename]["id"], array("*","$name=>Menu")))
			echo addelement($name, $name); 
	}
	echo "</ul>";
?>
<a style="color:#FFFFFF" href="logout.php" target="_parent"><img src="<? echo icons_url ?>stock_exit.png" alt="Logout"/></a>

</body>
</html>