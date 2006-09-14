<?php
class ValueModel extends PWBObject {
    /**
     * Gets the valuemodel's value
     */
    function getValue() {
		$this->subclassResponsibility('getValue()');
    }
	/**
     * Sets the valuemodel's value
     */
	function setValue($value) {
		$this->subclassResponsibility('setValue()');
	}
}

?>