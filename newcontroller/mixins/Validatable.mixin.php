<?php

class Validatable /* mixin */
{
    var $invalid_fields;
    var $error_messages;
    var $target;

    function Validatable(&$target) {
    	$this->target &= $target;
        $this->invalid_fields = array();
        $this->error_messages = array();
        $this->target->addEventListener(array('invalid_field' => 'addInvalidField'));
        $this->target->addEventListener(array('validation_error' => 'addErrorMessage'));
    }

    function addInvalidField($params) {
    	$field =& $this->readParam('field', $params);
        $this->invalid_fields[$field->name] =& $field;
        $this->addErrorMessage($params);
    }

    function addErrorMessage($params) {
    	$error_message =& $this->readParam('error_message');
        array_push($this->error_messages, $error_message);
    }

    function isInvalidField(&$field) {
    	return array_key_exists($field->name, $this->invalid_fields);
    }

    function checkNotNull($fields) {
        $ret = true;
        $is_valid = false;
        foreach ($fields as $field) {
            $is_valid = $this->$field->value != "";
            if (!$is_valid) {
                $this->triggerEvent('invalid_field', array('field' => $field,
                                                           'error_message' => "Fill in the " . $field . ", please"));

            }
            $ret &= $is_valid;
        }
        return $ret;
    }

    function checkOneOf($fields, $error_msg) {
        $ret=false;
        foreach ($fields as $field) {
            if (!isset($first_field)) $first_field = $field;
            $ret |= $this->$field->value != "";
        }
        if (!$ret) {
            $this->triggerEvent('checkOneOf_error', array('first_field' => $first_field,
                                                            'error_message' => $error_msg));
        }
        return $ret;
    }

    function validate() {
        return true;
    }
}
?>