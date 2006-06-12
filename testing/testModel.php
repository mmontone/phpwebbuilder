<?php
require_once dirname(__FILE__).'/../lib/basiclib.php';
includemodule(dirname(__FILE__).'/../BaseClasses');

new Collection();
new PWBObject();

includemodule(dirname(__FILE__).'/../Model');

$od =& new  ObjectDescription;
print_r($od->toArray());
echo "done Model";

?>
