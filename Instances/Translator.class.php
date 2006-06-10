<?php

class Translator extends PersistentObject {
	var $dictionary = null;

    function initialize() {
		$this->addTextField('language');
		$this->addCollectionField('translator',array('type' => 'MessageTranslation'));
    }

    function &forLanguage($language) {
    	if ($translator =& $_SESSION['translator'][$this->language->value])
    		return $translator;
    	$translator =& new Translator;
    	$translator->language->value = $language;
    	$translator->load();
    	return $translator;
    }

	function getDictionary() {
		if ($this->dictionary)
			return $this->dictionary;
		$translations = $this->translator->elements();
		$this->dictionary = array();
		foreach ($translations as $translation) {
			$message = $translation->message->getTarget();
			$this->dictionary[$message->message->value] = $translation->translation->value;
		}
	}

	function translate($msg) {
        $dictionary = $this->getDictionary();
        if (!array_key_exists($msg, $dictionary)) {
            return $msg;
        }

        return $dictionary[$msg];
    }

    function refresh() {
    	$this->dictionary = null;
    	$_SESSION['translator'][$this->language->value] = null;
    }
}
?>