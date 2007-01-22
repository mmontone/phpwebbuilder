<?php

#@preprocessor Compiler::usesClass(__FILE__, constant('translator'));@#

class Translator extends PWBObject
{
    var $dictionary = null;

    function dictionary() {
    	if ($this->dictionary == null) {
    		$this->dictionary = parse_ini_file($this->getDictionaryFile(), FALSE);
    	}
    	return $this->dictionary;
    }

    function trans($msg) {
        $key = strtolower($msg);
        $translated = $this->translateMessage($msg);
        if ($translated != null) {
        	return $translated;
        }
        else {
        	return Translator::TranslateWith(get_parent_class($this), $msg);
        }
    }

    function translateMessage($msg) {
    	if (getClass($this) == 'translator') {
    		return $msg;
		}
		else {
			$key = strtolower($msg);
			$dictionary = $this->dictionary();
			if (array_key_exists($key, $dictionary)) {
	           	$ret = $dictionary[$key];
	        	if (preg_match('/^[A-Z](.)*$/', $msg)) {
	        		return ucfirst($ret);
	        	}
	        	else {
	        		return $ret;
	        	}
			}
			return null;
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