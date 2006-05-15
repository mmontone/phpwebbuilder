<?php

class PersistentCollectionIterator {
	var $collection;
	var $offset;
	var $collection_size;
	var $limit;
	var $order;
	var $fetchedElements;

	function fetchElements() {
		$this->fetchedElements = & $this->collection->fetchElements($this->offset, $this->limit, $this->order);
	}

	function reset() {
		$this->pos = 0;
	}

	function &current() {
		if ($this->pos == $this->size())
			return null;

		if ($this->pos > $this->offset + $this->limit) {
			$this->offset += $this->limit;
			$this->fetchElements();
		}

		return $this->fetchedElements[$this->pos % $this->offset];
	}

	function &next() {
		if ($ret =& $this->current())
			$this->pos++;
		return $ret;
	}

	function end() {
		$this->pos = $this->getSize() - 1;
	}

	function size() {
		return $this->collection->getSize();
	}
}

?>