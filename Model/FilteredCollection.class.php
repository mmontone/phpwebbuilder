<?php

// We may place the iteration here.
class FilteredCollection extends QueryResult {
	var $filter;
	var $collection;
	var $size;

	function CollectionRange(& $collection, & $filter) {
		$this->collection = & $collection;
		$this->filter = & $filter;
	}

	function setFilter(&$filter) {
		$this->filter =& $filter;
		$this->size = null;
	}

	function fetchElements($offset, $limit, $order) {
		$this->collection->fetchElements($offset, $limit, $order, $this->filter->printSQL());
	}

	function getSize() {
		if (!$this->size)
			$this->size = $this->collection->getSize();
		return $this->size;
	}
}

?>