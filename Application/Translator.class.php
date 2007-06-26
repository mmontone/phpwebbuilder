<?php

#@preprocessor Compiler::usesClass(__FILE__, constant('translator'));@#

class Translator extends PWBObject
{
    var $dictionary = null;

    function dictionary() {
    	if ($this->dictionary == null) {
    		$df = $this->getDictionaryFile();
    		if ($df!=null){
    			$this->dictionary = parse_ini_file($df, FALSE);
    		}
    	}
    	return (array)$this->dictionary;
    }
    function getDictionaryFile(){}

    function trans($msg) {
        $key = strtolower($msg);
        $translated = $this->translateMessage($msg);
        if ($translated != null || getClass($this) == 'translator') {
        	return $translated;
        }
        else {
        	return Translator::TranslateWith(get_parent_class($this), $msg);
        }
    }

    function translateMessage($msg) {
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


	function TranslateWith($dicclass,$word){
		$d =& Translator::GetInstance($dicclass);
		return $d->trans($word);
	}

	function Translate ($msg){
		$ret = Translator::TranslateWith(translator,$msg);
		if ($ret===null) return $msg;
		else return $ret;
	}
	function TranslateHard ($msg){
		return Translator::TranslateWith(translator,$msg);
	}
	function TranslateAny($options, $prefix='', $suffix=''){
		foreach($options as $opt){
			$mess = Translator::TranslateHard($prefix.$opt.$suffix);
			if ($mess!==null) {
				return $mess;
			}
		}
		return $prefix.$opt.$suffix;
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