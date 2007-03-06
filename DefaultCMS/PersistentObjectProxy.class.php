<?php
class PersistentObjectProxy extends PersistentObject {
	var $target;
	var $unit_of_work;

	function PersistentObjectProxy(& $target) {
		parent :: Proxy($target);
		$this->target = & $target;
		$this->field_proxy_factory = & new FieldProxyFactory;
		$this->copyFrom($target);
	}

	function copyFrom(&$target) {
		 $this->parent =& new PersistentObjectProxy($target->parent);
		 $this->table = null; /* Be sure we don't interact with the database */
		 $this->fieldNames = $target->fieldNames;
		 $this->indexFields = $target->indexFields;
	}

	function addField (&$field) {
		$name = $field->varName;
        $this->$name =& $this->fieldProxyFor($field);
        $this->fieldNames[$name]=$name;
        if ($field->isIndex) {
        	$this->indexFields[$name]=$name;
        }
        $field->owner =& $this;
        $this->registerCommand('addField', $field);
    }

	function save() {
		$this->registerCommand('save');
	}

	function load() {

	}

	function & buildFieldsProxies(& $fields) {
		$fields_proxies = array ();
		foreach (array_keys($fields) as $i) {
			$fields_proxies[] = & $this->buildFieldProxy($fields[$i]);
		}
		return $fields_proxies;
	}

	function & buildFieldProxy(& $field) {
		return $this->field_proxy_factory->proxyFor($field);
	}
}

?>