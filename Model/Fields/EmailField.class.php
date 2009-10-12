<?
class EmailField extends TextField {

	function & visit(& $obj) {

		return $obj->visitedEmailField($this);
	}
	function SQLvalue() {
		return "'" . $this->getValue() . "'" . ", ";
	}
	function & validate() {
		if ($this->getValue() == '') {
			$val = false;
			return $val;
		} else {
			$atom = '[-A-Za-z0-9!#$%&\'*+/=?^_`{|}~]'; // allowed characters for part before "at" character
			$domain = '([A-Za-z]([-A-Za-z0-9]*[A-Za-z0-9]+)?)'; // allowed characters for part after "at" character
				$regex = '^' . $atom . '+' . // One or more atom characters.
		'(\.' . $atom . '+)*' . // Followed by zero or more dot separated sets of one or more atom characters.
		'@' . // Followed by an "at" character.
		'(' . $domain . '{1,63}\.)+' . // Followed by one or max 63 domain characters (dot separated).
		$domain . '{2,63}' . // Must be followed by one set consisting a period of two
	'$'; // or max 63 domain characters.
			$val = & $this->validate_ereg($regex, $this->displayString . ' ' . Translator :: translate('is not a valid email'));
			return $val;
		}
	}
}
?>