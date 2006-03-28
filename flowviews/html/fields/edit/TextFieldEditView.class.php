<?php

require_once dirname(__FILE__) . '/../../../FieldView.class.php';

class TextFieldEditView extends FieldView
{
    function TextFieldEditView(&$field, $config) {
        parent::FieldView($field, $config);
    }

    function render_on(&$html) {
        $html->text($this->field->visual_name . ': <input type=text name=' . $this->field->name . ' value=""/>');
    }
}

?>