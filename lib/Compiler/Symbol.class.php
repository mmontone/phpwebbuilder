<?php

class EregSymbol extends Parser {
	function EregSymbol($sym) {
		parent :: Parser();
		$this->sym = $sym;
	}
	function parse($tks) {
		if (preg_match('/^[\s\t\n]*(' . $this->sym . ')[\s\t\n]*/', $tks, $matches)) {
			return array ($matches[1],substr($tks,strlen($matches[0])));
		} else {
			$this->setError('Unexpected "'.$tks[0].'", expecting "'.$this->sym.'" in'.$this->parentParser->print_tree().' with "'.print_r($tks,TRUE). '" remaining');
			return array (FALSE,$tks);
		}
	}
	function print_tree() {
		return '"' . $this->sym . '"';
	}
}

class Symbol extends EregSymbol {
}

class Symbols extends EregSymbol {
	function Symbols($ss) {
		parent :: EregSymbol(implode('|', $ss));
	}
}

class Identifier extends EregSymbol {
	function Identifier() {
		parent :: EregSymbol('[a-zA-Z_][a-zA-Z_0-9]*');
	}
}
?>