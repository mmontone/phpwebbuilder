<?php

/* Improve!! */
/**
 * Reads an ini file, and defines each constant.
 */

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