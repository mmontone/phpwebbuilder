<?php

require_once dirname(__FILE__) . '/Configuration.class.php';

class FieldsConfiguration extends Configuration
{
    var $fields_configurations;


    function _configFor(&$field) {
        $field_config = &$this->fields_configurations[$field->name];
        if ($field_config == null) {
            return $this->createConfigFor($field);
        }
        return $field_config;
    }

    function embedTextField(&$field, &$component) {
        /* Don't embed text fields */
    }

    function embedCollectionField(&$field, &$component) {
        $collection_config =& $this->configFor($field);
        $collection_config->embed($component);
    }
}

?>