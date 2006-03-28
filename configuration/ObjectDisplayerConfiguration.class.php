<?php

class ObjectDisplayerConfiguration extends ComponentConfiguration
{
    var $component;
    var $fields_config;
    var $actions_config;

    /* Initialization */

    function ObjectDisplayerConfiguration(&$component) {
        $this->component =& $component;
    }


    function HtmlObjectViewConfiguration($config, &$fields_config) {
        parent::Configuration($config);
       $this->setFieldsConfig($fields_config);
    }

    /* Configuration  */

    function setFieldsConfig(&$fields_config) {
    	if ($fields_config == null)
            $this->fields_config =& new ObjectDisplayerFieldsConfiguration($this->component->model);
        else
            $this->fields_config =& $fields_config;
    }

    function setActionConfig(&$fields_config) {
        if ($fields_config == null)
            $this->fields_config =& new ObjectDisplayerActionsConfiguration();
        else
            $this->fields_config =& $fields_config;
    }

    /* Defaults */

    function defaultConfiguration() {
        return array('field_view_renderer' => 'ShowFieldViewRenderer');
    }

    /* Configuration propagation */

    function &fieldsConfiguration() {
        $config =& $this->aggregateTo($this->fields_config);
        return $config;
    }

    function &actionsConfiguration() {
    	$config =& $this->aggregateTo($this->actions_config);
        return $config;
    }
}
