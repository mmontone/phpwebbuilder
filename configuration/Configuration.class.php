<?php

class Configuration
{
    var $config;
    var $path_configs;
    var $class_configs;
    var $children_configs;
    var $father;

    function Configuration($config=array()) {
        $config = $this->defaultParamValue($config, array());
        $this->config = array_merge($this->defaultConfiguration(), $config);
        $this->path_configs = array();
        $this->class_configs = array();
        $this->children_configs = array();
    }

    /* Parameters management */

    function readParam($key, $params) {
        if (!array_key_exists($key, $params)) {
           trigger_error("$key parameter not passed");
           debug_print_backtrace(); /* Install PHP_Compat for PHP4 */
           exit;
        }

        return $params[$key];
    }

    function defaultParamValue($param, $value) {
        if ($param == null)
            return $value;
        else
            return $param;
    }

    function defaultConfiguration() {
        return array();
    }

    function addConfigForPath($path, &$config) {
    	$this->path_configs[$path] =& $config;
    }

    function addConfigForClass($class_name, &$config) {
        $this->class_configs[$class_name] =& $config;
    }

    function addChildrenConfig($children_id, &$config) {
    	$this->children_configs[$children_id] =& $config;
        $config->father =& $this;
    }

    function configForPath($path, &$config) {
    	if (array_key_exists($path, $this->path_configs)) {
    		$config =& $this->path_configs[$path];
            return true;
        }

        if ($this->father != null) {
        	return $this->father->configForPath($path, $config);
        }

        return false;
     }

     function configForClass($class, &$config) {
        if (array_key_exists($class, $this->class_configs)) {
            $config =& $this->class_configs[$class];
            return true;
        }

        if ($this->father != null) {
            return $this->father->configForClass($class, $config);
        }

        return false;
     }

     function configForChildren($children) {
       return $this->children_config[$children];
     }
}

?>