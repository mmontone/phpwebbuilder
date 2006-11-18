<?php
class Collection extends PWBObject {
	var $elements = null;
	/**
	 * Fields from the objects contained
	 */
	var $fields = array ();
	/** element count for pagination */
	var $limit = 0;
	/** starting pointfor pagination */
	var $offset = 0;
	/**
	 * Returns the size of the collection
	 */
	function size() {
		return count($this->elements());
	}
	function allFields() {
		return $this->fields;
	}
	function isEmpty() {
		return $this->size() == 0;
	}
	/**
	 *  Returns the first element of the collection, or null if it's empty
	 */
	function & first() {
		return $this->at(0);
	}
	/**
	 *  Returns the element of the collection at specified position, or null
	 */
	function & at($pos) {
		$es = & $this->elements();
		if (!empty($es)) {
			return $es[$pos];
		}
		else {
			return null;
		}
	}
	function atPut($pos, &$elem){
		$es = & $this->elements();
		$es[$pos] =& $elem;
		$this->triggerEvent('changed', $elem);
	}
	/**
	 *  Returns the index of the first element equal to the parameter
	 */
	function indexOf(& $elem) {
		$es = & $this->elements();
		$ks = array_keys($es);
		if (isPWBObject($elem)) {
			$f = lambda('&$e1', 'return $v = $elem->is($e1);', get_defined_vars());
		} else {
			$f = lambda('&$e1', 'return $v = $elem==$e1;', get_defined_vars());
		}
		foreach ($ks as $k) {
			$e = & $es[$k];
			if ($f ($e)) {
				return $k;
			}
		}
		return -1;
	}
	/**
	 *  Is the element in the collection?
	 */
	function includes(& $elem) {
		return $this->indexOf($elem) != -1;
	}
	/**
	 *  Returns an array of object, taking into account the offset and limit
	 */
	function & elements() {
		if ($this->elements === null) {
			$this->elements = array();
		}
		if ($this->limit != 0) {
			$e = $this->elements;
			return array_splice($e, $this->offset, $this->limit);
		} else {
			return $this->elements;
		}

	}
	/**
	 *  Adds an element to the end of the collection
	 */
	function add(& $elem) {
		$es = & $this->elements();
		$es[] = & $elem;
		$this->triggerEvent('changed', $elem);
	}
	/**
	 *  Removes the passed element from the collection
	 *
	 */
	function &remove(& $elem) {
		$i = & $this->indexOf($elem);

		if ($i == -1) {
			return null;
		} else {
			$es = & $this->elements();
			$newelems = array();
			foreach(array_keys($es) as $e) {
				if ($e !== $i) {
					$newelems[] =& $es[$e];
				}
			}
			$this->elements =& $newelems;
			$this->triggerEvent('changed', $this);
			return $elem;
		}
	}

	/**
	 *  Returns the last element of the collection and removes it
	 */
	function & pop() {
		if ($this->isEmpty()) {
			return null;
		}

		$es = & $this->elements();
		$ks = array_keys($es);
		$pos = $ks[count($ks) - 1];
		$elem = & $es[$pos];
		unset ($es[$this->size() - 1]);
		$this->triggerEvent('changed', $elem);
		return $elem;
	}
	/**
	 *  Returns the last element of the collection and removes it
	 */
	function & removeLast() {
		return $this->pop();
	}
	/**
	 *  Returns the first element of the collection and removes it
	 */
	function & shift() {
		$es = & $this->elements();
		$ks = array_keys($es);
		$pos = $ks[0];
		$elem = & $es[$pos];
		unset ($es[$pos]);
		$this->triggerEvent('changed', $elem);
		return $elem;
	}
	/**
	 *  Adds an element to the end of the collection
	 */
	function push(& $elem) {
		$this->add($elem);
	}
	/*
	 *  Applies a function to all the elements of the collection
	 */
	 function for_each(&$f) {
	 	$es = & $this->elements();

		foreach (array_keys($es) as $k) {
			$f ($es[$k]);
		}
	 }

	/**
	 *  Adds an element to the end of the collection
	 */
	/*function addFirst(&$elem) {
		$this->push(&$elem);
	}*/
	/**
	 *  Returns a collection with the result of applying the function to
	 * each element
	 */
	function & map($func) {
		$res = & $this->foldl(new Collection, lambda('&$col,&$elem', '$col->add($func($elem)); return $col;', get_defined_vars()));
		return $res;
	}
	/**
	 *  Returns a collection of the elements that satisfy the predicate
	 */
	function & filter($pred) {
		$res = & $this->foldl(new Collection,lambda('&$col,&$elem', 'if ($pred($elem)) $col->add($elem); return $col;', get_defined_vars()));
		return $res;
	}
	/**
	 *  If you dont' know foldl, then don't use it
	 */
	function & foldl(& $z, $f) {
		$acc = & $z;
		$es = & $this->elements();
		$ks = array_keys($es);
		foreach ($ks as $k) {
			$acc = & $f ($acc, $es[$k]);
		}
		return $acc;
	}
	/**
	 *  If you dont' know foldr, then don't use it
	 */
	function & foldr(& $z, $f) {
		$col = & $this->reverse();
		return $col->foldl($z, $f);
	}
	/**
	 *  Returns a collection with the elements in the reverse order
	 */
	function & reverse() {
		$es = & $this->elements();
		$c = & new Collection();
		$c->elements = array_reverse($es);
		return $c;
	}
	/**
	 *  Returns a collection of applying the messages to the object.
	 *  See lib/basiclib.php/apply_messages() for further reference.
	 */
	function & collect($mess) {
		$res = & $this->map(lambda('&$e', 'return apply_messages($e,$mess);', get_defined_vars()));
		return $res;
	}
	/**
	 *  Returns an array representation of the collection
	 */
	function & toArray() {
		return $this->elements();
	}
	/**
	 *  Adds all of the elements of the array to the collection
	 */
	function addAll($arr) {
		$ks = array_keys($arr);
		foreach ($ks as $k) {
			$this->add($arr[$k]);
		}
	}

	function addAllFromCollection(&$collection) {
		$self =& $this;
		$collection->for_each(lambda('&$x', '$self->add($x);', get_defined_vars()));
	}

	/**
	 *  Removes all of the elements of the array from the collection
	 */

	function removeAll($arr) {
		foreach (array_keys($arr) as $k) {
			$this->remove($arr[$k]);
		}
	}

	function removeAllFromCollection(&$collection) {
		$self =& $this;
		$collection->for_each(lambda('&$x', '$self->remove($x);', get_defined_vars()));
	}

	/**
	 *  Adds all of the elements of the collection to this collection
	 */
	function concat(& $col) {
		$this->addAll($col->elements());
	}
	/**
	 *  Returns the Type of the elements of the collection
	 */
	function getDataType() {
		return '';
	}
	/**
	 *  Reloads the colelction from it's source
	 */
	function refresh() {
	}
}
?>