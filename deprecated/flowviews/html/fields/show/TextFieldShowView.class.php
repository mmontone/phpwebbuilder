<?php

class TextFieldShowView extends FieldView
{
    function TextFieldShowView(&$field, &$config) {
    	parent::FieldView($field, $config);
    }

    function render_on(&$html) {
    	$html->text($this->field->visual_name . ': <input type=text name=' . $this->field->name . ' value="" readonly />');
    }
}

?>