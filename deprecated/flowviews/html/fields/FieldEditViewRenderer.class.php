<?php

require_once dirname(__FILE__) . '/FieldViewRenderer.class.php';

class EditFieldViewRenderer extends FieldViewRenderer
{
    function EditFieldViewRenderer(&$field, &$config) {
        parent::FieldViewRenderer($field);
    }

    function viewFor(&$field) {
    	$view_class = 'Edit' . $field->name . 'FieldView';
        return new $view_class($field, $this->config);
    }
}
?>