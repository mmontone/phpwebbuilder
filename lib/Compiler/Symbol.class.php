<?php

class EregSymbol extends Parser {
	function EregSymbol($sym) {
		parent :: Parser();
		$bars = explode('/',$sym);
		$mods = array_pop($bars);
		array_shift($bars);
		$this->preg = '/^[\s\t\n]*('.implode('/',$bars).')[\s\t\n]*/'.$mods;
		$this->sym = $sym;
	}
	function parse($tks) {
		if (preg_match($this->preg, $tks, $matches)) {
			return array ($matches[1],substr($tks,strlen($matches[0])));
		} else {
			$this->setError('Unexpected "'.@$tks[0].'", expecting "'.$this->sym.'" in'.$this->parentParser->print_tree().' with "'.print_r($tks,TRUE). '" remaining');
			return array (FALSE,$tks);
		}
	}
	function print_tree() {
		return '"' . $this->sym . '"';
	}
}

class Symbol extends EregSymbol {
	function Symbol($ss) {
		parent :: EregSymbol('/'.preg_quote($ss).'/');
	}
}
?>