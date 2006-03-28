<?php

require_once dirname(__FILE__) . '/Configuration.class.php';

class CollectionDisplayerConfiguration extends Configuration
{
    var $collection;

    function CollectionDisplayerConfiguration(&$collection) {
        $this->collection =& $collection;
    }

    function embedInto(&$component) {
        $collection_displayer =& $this->buildCollectionDisplayerComponent();
        $component->addChildren($collection_displayer);
    }

    function renderInto(&$component, &$html) {

    }

    function add_action(&$caller) {
    	$caller->call($this);
    }
}

?>