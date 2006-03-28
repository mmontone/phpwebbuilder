<?php

class HtmlTableEditFilenameFieldView extends HtmlTableEditFieldView
{
    function formObject($object) {
        $ret = "\n                     <input type=\"file\" name=\"";
        $ret .= $this->frmName($object);
        $ret .= "\" value=\"";
        $ret .= $this->field->getValue();
        $ret .= "\">";
        return $ret;
    }
}

?>