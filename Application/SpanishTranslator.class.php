<?php

require_once dirname(__FILE__) . '/Translator.class.php';

class SpanishTranslator extends Translator
{
    function getDictionaryFile(){
    	return dirname(__FILE__).'/spanish.dic';
    }

}
?>