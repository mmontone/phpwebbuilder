<?php

require_once dirname(__FILE__) . '/Translator.class.php';

class SpanishTranslator extends Translator
{
    function dictionary() {
    	return array (
            'Are you sure you want to save?'   => '¿Estas seguro que quieres guardar?',
            'Are you sure you want to delete?' => '¿Estas seguro que quieres borrar?'
            );
    }
}
?>