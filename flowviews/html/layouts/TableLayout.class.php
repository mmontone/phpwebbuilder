<?php

class TableLayout
{
    function beginObjectRendering(&$object, &$html) {
        $html->text('<table class=' . $object->get_class() . ' >');
    }

    function endObjectRendering(&$html) {
        $html->text('</table>');
    }

    function beginFieldRendering(&$field, &$html) {
        $html->text('<tr>');
    }

    function endFieldRendering(&$html) {
        $html->text('</tr>');
    }
}
?>