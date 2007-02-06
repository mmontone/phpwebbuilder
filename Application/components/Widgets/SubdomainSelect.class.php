<?php

class SubdomainSelect extends Component {
	/** The whole domain - the target items*/
	var $source_items;
	/** The image*/
	var $target_items;
	/** The selected domain*/
	var $selected_target_items;
	/** The selected image*/
	var $selected_source_items;

    function SubdomainSelect(&$target_items, &$source_items) {
		#@typecheck $target_items:Collection, $source_items:Collection@#
		$this->target_items =& $target_items;
		$this->source_items =& $source_items;

		parent::Component();
    }

    function &getTargetSelect(&$value_model, &$target_elements, $displayFunction=null) {
    	return new SelectMultiple($value_model, $target_elements, $displayFunction);
    }

    function &getSourceSelect(&$value_model, &$source_elements, $displayFunction=null) {
        return new SelectMultiple($value_model, $source_elements, $displayFunction);
    }

    function &initializeTargetsHolder() {
    	$h =& new ObjectHolder(new Collection);
        return $h;
    }

    function &initializeSourcesHolder() {
        $h =& new ObjectHolder(new Collection);
        return $h;
    }

    function initialize() {
		$this->selected_target_items =& $this->initializeTargetsHolder();
		$this->selected_source_items =& $this->initializeSourcesHolder();

		$this->source_items->removeAll($this->target_items->elements());

		$target_list =& $this->getTargetSelect($this->selected_target_items, $this->target_items, new FunctionObject($this, 'printObject'));
		$source_list =& $this->getSourceSelect($this->selected_source_items, $this->source_items, new FunctionObject($this, 'printObject'));

		$this->addComponent($target_list, 'c_target_list');
		$this->addComponent($source_list, 'c_source_list');

        $this->addComponent(new CommandLink(array('text' => 'Add', 'proceedFunction' => new FunctionObject($this, 'addElements'))), 'add');
    	$this->addComponent(new CommandLink(array('text' => 'Remove', 'proceedFunction' => new FunctionObject($this, 'removeElements'))), 'remove');
    }

    function printObject(&$object) {
    	return $object->printString();
    }

    function addElements() {
		$selected_source_items =& $this->getSelectedSourceItems();
		$source_elements =& $selected_source_items->elements();
		$this->target_items->addAll($source_elements);
		$this->source_items->removeAll($source_elements);
		$this->initializeSourcesHolder();
		$this->initializeTargetsHolder();
		$this->triggerEvent('itemsAdded', $selected_source_items);
    }

	function removeElements() {
		$selected_target_items =& $this->getSelectedTargetItems();
		$target_elements =& $selected_target_items->elements();
		$this->source_items->addAll($target_elements);
		$this->target_items->removeAll($target_elements);
		$this->initializeSourcesHolder();
		$this->initializeTargetsHolder();
		$this->triggerEvent('itemsRemoved', $selected_target_items);
    }

    function setSourceItems(&$items) {
    	$this->initializeSourcesHolder();
    	$this->source_items =& $items;
    }

    function &getSourceItems() {
    	return $this->source_items;
    }

    function &getSelectedSourceItems() {
    	return $this->selected_source_items->getValue();
    }

    function &setSelectedSourceItems(&$collection) {
    	return $this->selected_source_items->setValue($collection);
    }

    function &getSelectedTargetItems() {
    	return $this->selected_target_items->getValue();
    }

    function &setSelectedTargetItems(&$collection) {
    	return $this->selected_target_items->setValue($collection);
    }
}

class SingleSubdomainSelect extends Component {
	/** The whole domain - the target items*/
    var $source_items;
    /** The image*/
    var $target_items;

    function SingleSubdomainSelect(&$target_items, &$source_items) {
        #@typecheck $target_items:Collection, $source_items:Collection@#
        $this->target_items =& $target_items;
        $this->source_items =& $source_items;

        parent::Component();
    }

    function &getTargetListComponent(&$target_elements, $displayFunction=null) {
        return new CollectionNavigator($target_elements, $displayFunction);
    }

    function &getSourceListComponent(&$source_elements, $displayFunction=null) {
        return new CollectionNavigator($source_elements, $displayFunction);
    }

    function initialize() {
        $this->source_items->removeAll($this->target_items->elements());

        $target_list =& $this->getTargetListComponent($this->target_items, new FunctionObject($this, 'printObject'));
        $target_list->registerCallback('element_selected', new FunctionObject($this, 'removeElement'));
        $source_list =& $this->getSourceListComponent($this->source_items, new FunctionObject($this, 'printObject'));
        $source_list->registerCallback('element_selected', new FunctionObject($this, 'addElement'));

        $this->addComponent($target_list, 'c_target_list');
        $this->addComponent($source_list, 'c_source_list');
    }

    function printObject(&$object) {
        return $object->printString();
    }

    function addElement(&$element) {
        $this->target_items->add($element);
        $this->source_items->remove($element);

        $this->triggerEvent('itemAdded', $element);
    }

    function removeElement(&$element) {
        $this->source_items->add($element);
        $this->target_items->remove($element);

        $this->triggerEvent('itemRemoved', $element);
    }
}


?>