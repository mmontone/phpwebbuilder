<?php

class DivLayout
{
    function beginObjectRendering(&$object, &$html) {
    	$html->text('<div class=' . get_class($object) . ' >');
    }

    function endObjectRendering(&$object, &$html) {
    	$html->text('</div>');
    }

    function beginFieldRendering(&$field, &$html) {
    	$html->text('<div class=field>');
    }

    function endFieldRendering(&$field, &$html) {
    	$html->text('</div>');
    }
}
?>