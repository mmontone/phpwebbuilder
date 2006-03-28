<?php

class ValidatorView extends PWBObject
{
    function ValidatorView(&$validator, &$object_view) {
    	$view->addEventListener($this, array('field_rendered' => 'displayFieldError'));
    }
}

class IconValidatorView extends ValidatorView
{
    function displayFieldError($params) {
        $html =& $params['html'];
        $field =& $params['field'];

        if (!$this->validator->isValidField(&$field)) {
            $html->text('<img src=cruz.jpg>');
        }
    }
}

?>