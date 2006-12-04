<?php

class SpanishTranslator extends Translator
{
    function getDictionaryFile(){
    	return constant('pwbdir'). '/Application/spanish.dic';
    }

}
?>