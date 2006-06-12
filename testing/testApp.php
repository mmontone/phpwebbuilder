<?php
require_once dirname(__FILE__).'/../lib/basiclib.php';
includemodule(dirname(__FILE__).'/../BaseClasses');

new Collection();
new PWBObject();

includemodule(dirname(__FILE__).'/../Application');

$od =& new  Component;
$od =& new  Widget($n=null);
$od =& new  ActionDispatcher;
echo "done Application";

?>
