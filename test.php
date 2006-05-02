<?php

ini_set('display_errors',true);
error_reporting(E_ALL);
echo memory_get_usage();
ini_set('memory_limit', '32M');
echo "<br/>".ini_get('memory_limit');


?>