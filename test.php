<?
require_once "/var/www/cefi/Configuration/pwbapp.php";

$str = "aún a&uacute;n a&amp;uacute;n a&amp;amp;uacute;n a&amp;&amp;uacute;n ";
echo "<br/>\n";
echo toAjax($str);
echo "<br/>\n";
echo ereg_replace('&(amp;|&amp;)+(([A-Za-z0-9]+);)', /*'found "\\1" and "\\2" in \\0: '*/'&\\2'/*<br/>'."\n"*/, $str);
echo "<br/>\n";
echo ereg_replace('([A-Za-z0-9]+)', '\\1\\1',"&uacute;");

?>
