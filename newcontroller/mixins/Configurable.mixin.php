<?php

/* The best thing would be a ConfigurableMixin */
class Configurable /* mixin */
{
    var $config;

    function read($key) {
        if (!array_key_exists($key, $this->config)) {
           trigger_error("$key not configured");
           debug_print_backtrace(); /* Install PHP_Compat for PHP4 */
           exit;
        }

        return $this->config[$key];
    }

    function configFor($key, $config) {
        if (is_array($config[$key])) {
            return array_merge($config, $config[$key]);
        }
        return $config;
    }

    function defaultConfig($default_config, $config) {
        return array_merge($config, $default_config);
    }
}
?>