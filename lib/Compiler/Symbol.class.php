<?php

class EregSymbol extends Parser {
	function EregSymbol($sym) {
		parent :: Parser();
		$bars = explode('/',$sym);
		$mods = array_pop($bars);
		array_shift($bars);
		$this->spaces = '([\s\t\n]|\/\/.*?\n|\#.*\n|\/\*(.|\n)*?\*\/)*';
		$this->preg = '/^'.$this->spaces.'(?P<result>'.implode('/',$bars).')'.$this->spaces.'/'.$mods;
		$this->sym = $sym;
	}
	function parse($tks) {
		if (preg_match($this->preg, $tks->str, $matches)) {
			#@parse_echo
			echo '<li>';
			var_dump('parsed '.$matches['result']);
			echo '</li>';
			//@#
			return array (ParseResult::match($matches['result']),new ParseInput(substr($tks->str,strlen($matches[0]))));
		} else {
			$this->setError(array(array('rem'=>(string)strlen(preg_replace('/^'.$this->spaces.'/','',$tks->str)),'sym'=>$this->sym)));
			#@parse_echo
			echo '<li>';
			var_dump('failed '.$this->sym. ':'.substr($tks->str,0,5));
			echo '</li>';
			//@#
			return array (ParseResult::fail(),$tks);
		}
	}
	function print_tree() {
		return $this->sym;
	}
}

class Symbol extends EregSymbol {
	function Symbol($ss) {
		parent :: EregSymbol('/'.preg_replace('/\/|\\//', '\/',preg_quote($ss)).'/');
		$this->sym='"'.$ss.'"';
	}
}
?>