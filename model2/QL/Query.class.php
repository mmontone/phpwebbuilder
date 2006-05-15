<?php

/*
class SQLSelect
{
	var $attributes;
}

class SQLFrom
{
	var $table_names;
}

class SQLWhere
{
	var $restrictions;
}

class SQLClause
{
	var $select;
	var $from;
	var $where;
}

*/

class SQLClause
{
	var $select_attributes = array();
	var $from_tables = array();
	var $where_restrictions = array();

	function addSelectAttribute($attr) {
		$this->select_attributes[] =& $attr;
	}

	function addFromTable($table_name) {
		$this->from_tables[] =& $table_name;
	}

	function addWhereRestriction($restriction) {
		$this->where_restrictions[] =& $restriction;
	}

	function toString() {
		$ret = 'SELECT ';
		$ret .= implode(',', $this->select_attributes);
		$ret .= "\nFROM ";
		$ret .= implode(',', $this->from_tables);
		$ret .= "\nWHERE";
		$ret .= implode(' AND ', $this->where_restrictions);
		return $ret;
	}
}


class Query
{
	function Query() {
    }

    function toSQL() {

    }
}

class ObjectQuery extends Query
{
	var $object;
	var $fields_restrictions = array();
	var $is_target;

	function ObjectQuery(&$object) {
		$this->object =& $object;
	}

	function addFieldRestriction(&$field_restriction) {
		$this->fields_restrictions[] =& $field_restriction;
	}

	function setTarget($val = true) {
		$this->is_target =& $val;
	}

	function appendToSQL(&$sql) {
		if ($this->is_target) {
			$this->addSelectAttributes();
		}

		foreach (array_keys($this->fields_restrictions) as $field_restriction) {
			$field_restriction->appendToSQL($sql);
		}
	}

	function addSelectAttributes(&$sql) {
		// No se esta soportando herencia
		foreach ($this->allFieldNamesThisLevel() as $field_name) {
			$sql->addSelectAttribute($this->table_name . '.' . $field_name);
		}
	}
}

class DataFieldRestriction
{
	function appendToSQL(&$sql) {

	}
}

class SimpleDataFieldRestriction extends DataFieldRestriction {
	var $field;

	function DataFieldRestriction(&$field) {
		$this->field =& $field;
	}
}

class CompositeDataFieldRestriction extends DataFieldRestriction {

}

class NumberFieldRestriction extends DataFieldRestriction {

}

class AndRestriction extends DataFieldRestriction
{
	var $r1;
	var $r2;

	function AndRestriction(&$r1, &$r2) {
		$this->r1 =& $r1;
		$this->r2 =& $r2;
	}

	function appendToSQL(&$sql) {
		$sql->addWhereRestriction($this);
	}

	function printSQL() {
		return '(' . $this->r1->printSQL() . ' AND ' . $this->r2->printSQL() . ')';
	}
}

class OrRestriction extends DataFieldRestriction
{
	var $r1;
	var $r2;

	function OrRestriction(&$r1, &$r2) {
		$this->r1 =& $r1;
		$this->r2 =& $r2;
	}

	function appendToSQL(&$sql) {
		$sql->addWhereRestriction($this);
	}

	function printSQL() {
		return '(' . $this->r1->printSQL() . ' OR ' . $this->r2->printSQL() . ')';
	}
}



class NumberFieldRestriction extends SimpleDataFieldRestriction {
	var $n;
	var $operator;

	function NumberFieldRestriction(&$field, $operator, $n) {
		parent::SimpleDataFieldRestriction($field);
		$this->operator = $operator;
		$this->n = $n;
	}
}

// No es necesaria, creamos un metodo addRangeNumberRestriction en ObjectQuery

class RangeNumberFieldRestriction extends NumberFieldRestriction {
	var $from;
	var $to;

	function RangeNumberFieldRestriction(&$field, $from, $to) {
		parent::NumberFieldRestriction($field);
		$this->from = $from;
		$this->to = $to;
	}

	function printSQL() {
		$r1 =& new NumberFieldRestriction($this->field, '>=', $this->from);
		$r2 =& new NumberFieldRestriction($this->field, '<=', $this->to);
		$and =& new AndRestriction($r1, $r2);
		return $and->printSQL();
	}

}

class IndexFieldRestriction extends SimpleDataFieldRestriction {
	var $object_query;

	function IndexFieldRestriction(&$field, &$object_query) {
		parent::SimpleDataFieldRestriction($field);
		$this->object_query =& $object_query;
	}

	function appendToSQL(&$sql) {
		$sql->addFromTable($this->object_query->object->table_name);
		$sql->addWhereRestriction($this->printWhereSQL());
		$this->object_query->appendToSQL($sql);
	}

	function printWhereSQL() {
		return $this->object_query->object->table_name . '.id = ' . $this->field->owner->table_name . '.' . $this->field->name;
	}
}

class CollectionFieldRestriction extends SimpleDataFieldRestriction {
	var $elements_query;

	function CollectionFieldRestriction(&$field, &$elements_query) {
		parent::SimpleDataFieldRestriction($field);
		$this->elements_query =& $elements_query;
	}

	function appendToSQL(&$sql) {
		$sql->addFromTable($this->elements_query->object->table_name);
		$sql->addWhereRestriction($this->printWhereSQL());
		$this->elements_query->appendToSQL($sql);
	}

	function printWhereSQL() {
		return $this->field->qualifiedName() . ' = ' .  $this->object_query->table_name . '.' . $this->field->name;
	}
}

class RestrictedQuery extends Query
{
	var $query;
	var $restriction;

	function toSQL() {
		$sql =& $this->query->toSQL();
		$sql->addRestriction($this->restriction);
	}
}

class BinaryOperation {

}

class DataFieldAccess
{
	var $field;

}

class TextFieldAccess extends DataFieldAccess {

}

/*
// No tiene sentido en un where
class IndexFieldAccess extends DataFieldAccess {
	function toSQL() {
		$oq =& new ObjectQuery($this->field->target);
		$q =& new RestrictedQuery($oq, ); // $this->field->target->id = $this->

	}
}
*/
class CollectionFieldAccess extends DataFieldAccess {

}

?>