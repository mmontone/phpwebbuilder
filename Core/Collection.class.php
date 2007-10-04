<?php
class Collection extends PWBObject {
	var $elements = null;
	/**
	 * Fields from the objects contained
	 */
	var $fields = array ();
	/** element count for pagination */
	var $limit = null;
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

    function setEmpty() {
    	$n = null;
        $this->elements =& $n;
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
		if (!empty($es) && !is_exception($es)) {
			return $es[$pos];
		}
		else {
			$n = null;
			return $n;
		}
	}
	function atPut($pos, &$elem){
		$es = & $this->elements();
		$es[$pos] =& $elem;
		$this->triggerEvent('changed', $elem);
	}

	function getLimit() {
		return $this->limit;
	}

    function setLimit($limit) {
    	$this->limit = $limit;
    }
	/**
	 *  Returns the index of the first element equal to the parameter
	 */
	function indexOf(& $elem) {
		$es = & $this->elements();
		$ks = array_keys($es);
		if (isPWBObject($elem)) {
			$f = lambda('&$e1', '$v = $elem->is($e1);return $v;', get_defined_vars());
		} else {
			$f = lambda('&$e1', '$v = $elem==$e1;return $v;', get_defined_vars());
		}
		foreach ($ks as $k) {
			$e = & $es[$k];
			if ($f ($e)) {
				return $k;
			}
		}
		$n = -1;
		return $n;
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
			$newa = array_splice($e, $this->offset, $this->limit);
			return $newa;
		} else {
			return $this->elements;
		}

	}
	/**
	 *  Adds an element to the end of the collection
	 */
	function add(& $elem) {
        $es =& $this->elements;
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
			$n = null;
			return $n;
		} else {
			$es = & $this->elements;
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
	function & detect($pred) {
		//$res = & $this->foldl(new Collection,lambda('&$col,&$elem', ' $col->add($elem); return $col;', get_defined_vars()));
		$es = & $this->elements();
		foreach (array_keys($es) as $k) {
			if ($pred($es[$k])){
				return $es[$k];
			}
		}
		$n = null;
		return $n;
	}
	/**
	 *  An interative approach to a fold
	 */
	function & foldl(& $z, $f) {
		$acc = & $z;
		$es = & $this->elements();
		foreach (array_keys($es) as $k) {
			$acc = & $f ($acc, $es[$k]);
		}
		return $acc;
	}
	/**
	 *  A recursive approach to a fold. Basic implementation, only to use with commutative functions.
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
        /*$e = $this->disableEvent('changed');
        $ks = array_keys($arr);
        foreach ($ks as $k) {
            $this->add($arr[$k]);
        }

        $this->enableEvent($e);*/
        $this->elements = array_merge($this->elements(),$arr);
        $this->triggerEvent('changed', $this);
	}

	function addAllFromCollection(&$collection) {
		$this->addAll($collection->elements());
	}

	/**
	 *  Removes all of the elements of the array from the collection
	 */

	function removeAll($arr) {
        if (is_null($this->elements)) return;
        $e = $this->disableEvent('changed');
        $ks = array_keys($arr);
        foreach (array_keys($arr) as $k) {
            $this->remove($arr[$k]);
        }
        $this->enableEvent($e);
        $this->triggerEvent('changed', $this);
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
	/** Checks for typing, using type parameters is needed */
	function hasType($type){
		$params = explode('<',$type, 2);
		if (count($params)>1){
			return parent::hasType($params[0]) && is_subclass($this->getDataType(), str_replace('>','',$params[1]));
		} else {
			return parent::hasType($params[0]);
		}
	}

	/**
	 *  Reloads the colelction from it's source
	 */
	function refresh() {
	}

    function debugPrintString() {
    	return $this->primPrintString('size: ' . $this->size());
    }

    function printString() {
    	return $this->debugPrintString();
    }
    function &fromArray($array){
    	$col =& new Collection();
    	$col->addAll($array);
    	return $col;
    }


}
?>