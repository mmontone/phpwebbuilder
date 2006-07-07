<?
class EmailField extends TextField {

	function & visit(& $obj) {

		return $obj->visitedEmailField($this);
	}

	function emailField($name, $isIndex) {
		parent :: textField($name, $isIndex);
	}

	function SQLvalue() {
		return "'" . $this->getValue() . "'" . ", ";
	}

	function validate() {
		$atom = '[-a-z0-9!#$%&\'*+/=?^_`{|}~]'; // allowed characters for part before "at" character
		$domain = '([a-z]([-a-z0-9]*[a-z0-9]+)?)'; // allowed characters for part after "at" character
			$regex = '^' . $atom . '+' . // One or more atom characters.
		'(\.' . $atom . '+)*' . // Followed by zero or more dot separated sets of one or more atom characters.
		'@' . // Followed by an "at" character.
		'(' . $domain . '{1,63}\.)+' . // Followed by one or max 63 domain characters (dot separated).
		$domain . '{2,63}' . // Must be followed by one set consisting a period of two
	'$'; // or max 63 domain characters.
		$bool = eregi($regex, $this->getValue());

		if (!$bool) {
			$ex =& new ValidationException(array (
				'message' => $this->displayString . ' is not a valid email',
				'content' => & $this
			));
			$this->triggerEvent('invalid', $ex);
			return $ex;
		}

		$this->triggerEvent('validated', $this);
		return false;

	}
}
?>