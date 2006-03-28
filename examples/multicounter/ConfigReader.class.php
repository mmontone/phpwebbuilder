<?php

/* Improve!! */

class ConfigReader
{
    function read($file_name) {
    	$configuration = parse_ini_file($file_name);

        foreach ($configuration as $key => $value) {
        	define($key, $value);
        }
    }
}
?>