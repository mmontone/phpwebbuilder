<?php

class SubdomainSelect extends Component {
	var $source_items;
	var $target_items;
	var $selected_target_items;
	var $selected_source_items;

    function SubdomainSelect(&$target_items, &$source_items) {
		$this->target_items =& $target_items;
		$this->source_items =& $source_items;

		parent::Component();
    }

    function initialize() {
		$this->selected_target_items =& new ObjectHolder(new Collection);
		$this->selected_source_items =& new ObjectHolder(new Collection);

		$this->source_items->removeAll($this->target_items->elements());

		$target_list =& new SelectMultiple($this->selected_target_items, $this->target_items, new FunctionObject($this, 'printObject'));
		$source_list =& new SelectMultiple($this->selected_source_items, $this->source_items, new FunctionObject($this, 'printObject'));

		$this->addComponent($target_list, 'c_target_list');
		$this->addComponent($source_list, 'c_source_list');
		$this->addComponent(new CommandLink(array('text' => 'Add', 'proceedFunction' => new FunctionObject($this, 'addElements'))));
		$this->addComponent(new CommandLink(array('text' => 'Remove', 'proceedFunction' => new FunctionObject($this, 'removeElements'))));
    }

    function printObject(&$object) {
    	return $object->printString();
    }

    function addElements() {
		$selected_source_items =& $this->getSelectedSourceItems();
		$source_elements =& $selected_source_items->elements();
		$this->target_items->addAll($source_elements);
		//echo 'Source elemnents size: ' . $this->source_items->size();
		$this->source_items->removeAll($source_elements);
		//foreach($this->source_items->elements() as $element) {
		//	echo 'Class: ' . getClass($element);
		//}
		//echo 'Source elemnents size: ' . count($this->source_items->elements());
		//echo 'Source elemnents size: ' . getClass($this->source_items);
		//echo 'Source elemnents size: ' . $this->source_items->size();
		$this->setSelectedSourceItems(new Collection);
		$this->setSelectedTargetItems(new Collection);
		$this->triggerEvent('itemsAdded', $selected_source_items);
    }

	function removeElements() {
		$selected_target_items =& $this->getSelectedTargetItems();
		$target_elements =& $selected_target_items->elements();
		$this->source_items->addAll($target_elements);
		$this->target_items->removeAll($target_elements);
		$this->setSelectedSourceItems(new Collection);
		$this->setSelectedTargetItems(new Collection);
		$this->triggerEvent('itemsRemoved', $selected_target_items);
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

?>