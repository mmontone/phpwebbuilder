<?php

class Translator extends PWBObject
{
    var $dictionary;

    function Translator() {
        $this->dictionary = $this->dictionary();
    }
    function dictionary() {
    	return parse_ini_file($this->getDictionaryFile(), FALSE);
    }

    function trans($msg) {
	        if (!array_key_exists($msg, $this->dictionary)) {
	            return $msg;
	        } else {
	        	return $this->dictionary[$msg];
	        }
    }
	function TranslateWith($dicclass,$word){
		$d =& Translator::GetInstance($dicclass);
		return $d->trans($word);
	}
	function Translate ($msg){
		return Translator::TranslateWith(translator,$msg);
	}
	function &GetInstance($dicclass){
		$app =& Application::instance();
		if (!isset($app->translators[$dicclass])){
			$app->translators[$dicclass] =& new $dicclass;
		}
		return $app->translators[$dicclass];
	}
}
?>