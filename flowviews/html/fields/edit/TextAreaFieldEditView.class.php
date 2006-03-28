<?php

class TextAreaFieldEditView extends FieldView
{
    function render(&$html) {
        $html->text($this->field->visual_name . ': <input type=textarea name=' . $this->field->name . ' value=""/>');
    }
}

?>