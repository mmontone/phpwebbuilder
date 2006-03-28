<?php 

class FieldsEmbeddingVisitor
{
    var $component;

    function FieldsEmbeddingVisitor(&$component) {
    	$this->component =& $component;
    }

    function visitedCollectionField(&$field) {
        $this->component->embedCollectionField(&$field);
    }

    function visitedIdField(&$field) {
    	$this->component->embedIdField($field);
    }

    function visitedTextField(&$field) {
    	$this->component->embedTextField(&$field);
    }
}

?>
