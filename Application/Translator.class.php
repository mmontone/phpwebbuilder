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
	        $key = strtolower($msg);
	        if (!array_key_exists($key, $this->dictionary)) {
	            return $msg;
	        } else {
	        	$ret = $this->dictionary[$key];
	        	if (preg_match('/^[A-Z](.)*$/', $msg)) {
	        		return ucfirst($ret);
	        	}
	        	else {
	        		return $ret;
	        	}
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