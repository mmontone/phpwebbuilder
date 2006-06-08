<?php

require_once ("pwb.php");
trace_params();
if (isset($_REQUEST["Username"])) {
	login($_REQUEST["Username"], $_REQUEST["Password"]);
	if ($_SESSION[sitename]["Username"]) {
		?>
		<html>
		<head>
		<script>
		function redirect () {
			window.top.location="index.php";
		}
		</script>
		</head>
		<body onload="redirect()";>
		</body>
		</html>
		<?
	} else {
		header("location:login.php?failed=1");
	}
}
if (isset($_REQUEST["denied"])) { 
?>



	<h3>You don't have enough permissions to do that</h3>
	<h2><? echo $_REQUEST["error"]?></h2>
	<a style= "color: White;" title="Go back" href="javascript: history.go(-1)"><img title="Go back" src="<? echo icons_url ?>stock_undo.png"/></a>
	

<? exit; } ?>

<html>
<head>

<? if (isset($_REQUEST["failed"])) { ?>

<H1>OOPS! Wrong password.</H1>

<? } ?>

<title>Welcome</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body bgcolor="#FFFFFF">
<p><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#006699"><b>Welcome to the Content Management System<br>
  for <? echo sitename ?></b></font></p>
<form method="post" action="login.php">
  <table width="64%" border="0" align="left">
    <tr>
      <td align="left" width="31%">
        <div align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">Username: </font></div>
      </td>
      <td width="69%"> <font face="Verdana, Arial, Helvetica, sans-serif">
        <input type="text" name="Username">
        </font></td>
    </tr>
    <tr>
      <td width="31%">
        <div align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Password:</font></div>
      </td>
      <td width="69%"> <font face="Verdana, Arial, Helvetica, sans-serif">
        <input type="password" name="Password">
        </font></td>
    </tr>
  </table>
  <div align="left"><font face="Verdana, Arial, Helvetica, sans-serif"><br>
    <br>
    <br>
    <br>
    <br>
    <input type="submit" name="Submit" value="Login">
    </font></div>
</form>
</body>
</html>
