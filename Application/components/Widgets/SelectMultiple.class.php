<?php

class SelectMultiple extends Select {
	function initializeOptions(){}
	function viewUpdated($new_value) {
		if ($new_value == '') {
			$this->setValue(new Collection);
		}
		else {
			$selected = explode(',', $new_value);
			$newitems =& new Collection;

			foreach ($selected as $selected) {
				$newitems->add($this->options->at((int) $selected));
			}

			$this->setValue($newitems);
		}
	}
}
?>