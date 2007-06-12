<?php

class ObjectsNavigator extends ContextualComponent {
	var $objects;
	var $restrictions = array();
	var $validation_errors;
	#@use_mixin EditorComponent@#
	function ObjectsNavigator(&$objects) {
		$this->objects =& $objects;
		parent::ContextualComponent();
	}

	function initialize() {
		$this->initializeRestrictions();

		$search =& new CommandLink(array('text' => Translator::translate('Search'), 'proceedFunction' => new FunctionObject($this, 'searchObjects')));
		$this->addComponent($search, 'search');
		$rs =& $this->restrictions;
		foreach (array_keys($rs) as $r) {
			$restriction =& $rs[$r];
			$comp =& $this->addRestrictionComponent($restriction, $this->getComponentFor($restriction));
			$comp->onEnterClickOn($search);
		}

		$this->searchObjects();
        $this->addActionMenu($this->printObjectsMessage(), new FunctionObject($this, 'printList'));
	}
	function getFieldNames(){
		$meta =& $this->objects->getMetaData();
		$fields = $meta->allIndexFieldNames();
		unset($fields['id']);
		unset($fields['PWBversion']);
		unset($fields['super']);
		unset($fields['refCount']);
		unset($fields['rootObject']);
		return $fields;
	}

	function initializeRestrictions(){
		$meta =& $this->objects->getMetaData();
		$fields =& $meta->class->fieldsWithNames($this->getFieldNames());
       	foreach(array_keys($fields) as $f2){
    		$this->addRestriction(mdcompcall('getFieldRestrictionComponent',array(&$this,&$fields[$f2])));
       	}
	}
	function refresh() {
		$this->list->refresh();
	}

	function addRestrictionComponent(&$restriction, &$component) {
		$c =& $this->getComponentFor($restriction);
		$fc =& new FieldComponent;
		$fc->addComponent($c, 'component');
		$c->onEagerChangeSend('searchObjects',$this);
		$fc->addComponent(new Label(ucfirst(Translator::Translate($restriction->printName()))), 'field_name');
		$this->addComponent($fc, $restriction->getName() . 'Restriction');
		return $c;
	}

	function &getComponentFor(&$restriction) {
		return mdcall('getComponentFor', array(&$this, &$restriction));
	}

	function &getObjects() {
		$objects =& new CompositeReport($this->objects);
		return $objects;
	}

	function searchObjects() {
		$objects =& $this->getObjects();

		$errors =& $this->validateRestrictions();

		if (empty($errors)) {
			$this->cleanValidationErrors();

			$this->applyRestrictions($objects);

			$this->displayObjects($objects);
		}
		else {
			$this->displayValidationErrors($errors);
		}
	}

	function validateRestrictions() {
		$errors = array();

		foreach (array_keys($this->restrictions) as $r) {
			$restriction =& $this->restrictions[$r];
			$ex =& $restriction->validate();
			if (is_exception($ex)) {
				$errors[] =& $ex;
			}
		}

		return $errors;
	}

	function displayValidationErrors($errors) {
		$this->addComponent(new ValidationErrorsDisplayer($errors), 'validation_errors');
	}

	function cleanValidationErrors() {
		if ($this->validation_errors !== null) {
			$this->validation_errors->delete();
		}
	}


	function applyRestrictions(&$objects) {
		//$objects->defineVar('target', $objects->getDataType());
		foreach (array_keys($this->restrictions) as $r) {
			$this->restrictions[$r]->applyTo($objects);
		}
	}

	function addRestriction(&$restriction) {
		$this->restrictions[] =& $restriction;
	}

	function &displayObjects(&$objects) {
		$list =& $this->getListComponent($objects);
		$list->registerCallback('element_selected', new FunctionObject($this, 'elementSelected'));
		$this->addComponent($list, 'list');
		return $list;
	}

	function elementSelected(&$object) {
		$this->callbackWith('element_selected', $object);
	}

	function &getListComponent(&$objects) {
		return mdcompcall('getListComponent', array(&$this, &$objects));
	}

    function printList(){
        $objs =& new CompositeReport($this->list->col);
        $cant = $objs->size();
        if ($cant>60) {
            $this->call($qd =& QuestionDialog::create('La cantidad de elementos a imprimir es muy grande ('.$cant.') y puede llevar mucho tiempo, ¿Está seguro que desea continuar?'));
            $qd->onYes(new FunctionObject($this, 'printConfirmed'));
            $qd->onNo(new FunctionObject($this, 'doNothing'));
        } else {
            $this->printConfirmed();
        }
    }
    function printConfirmed(){
        $objs =& new CompositeReport($this->list->col);
        $list =& $this->getListComponent($objs);
        $list->setDynVar('context', ContextualComponent::newContext());
        $objs->limit = 0;
        $win =& new Window($list, 'imprimir');
    	$win->addAjaxCommand(new AjaxCommand('window.print', array()));
        $win->open("status=0,scrollbars=1,resizable=1,width=800,height=620");
    }

    function printObjectsMessage() {
        return Translator::translate('Print');
    }
}

#@defmdf getComponentFor (&$navigator : ObjectsNavigator, &$restriction : Restriction)
{
	$in =& new Input($restriction);
	return $in;
}//@#

#@defmdf getComponentFor (&$navigator : ObjectsNavigator, &$restriction : BoolRestriction)
{
	return new Checkbox($restriction);
}//@#


#@defmdf getComponentFor(&$navigator : ObjectsNavigator, &$restriction : DateRestriction)
{
	return new DateTimeInput($restriction);
	//return new Input($restriction);
}//@#

#@defmdf &getListComponent[Component](&$contacts: Collection)
{
	return new ObjectsNavigator($contacts);
}
//@#
#@defmdf &getListComponent[ObjectsNavigator](&$contacts: Collection)
{
	return new ObjectsList($contacts);
}
//@#

#@defmdf getFieldRestrictionComponent[Component](&$field: DataField)
{
	return new TextRestriction(
    			array('name' => $field->getName(), 'field' => new AttrPathExpression('',$field->getName()), 'operation' => '='));
}//@#

#@defmdf getFieldRestrictionComponent[Component](&$field: BoolField)
{
	return new BoolRestriction(
    			array('name' => $field->getName(), 'field' => new AttrPathExpression('',$field->getName()), 'operation' => '='));
}//@#


#@defmdf getFieldRestrictionComponent[Component](&$field: TextField)
{
	return new WildcardTextRestriction(
    			array('name' => $field->getName(), 'field' => new AttrPathExpression('',$field->getName()), 'operation' => 'LIKE'));
}//@#

/*
function &getComponentFor_OBJECTSNAVIGATOR_RESTRICTION(&$navigator, &$restriction) {
	return new Input($restriction);
}

function &getComponentFor_OBJECTSNAVIGATOR_BOOLRESTRICTION(&$navigator, &$restriction) {
	return new Checkbox($restriction);
}

function &getComponentFor_OBJECTSNAVIGATOR_DATERESTRICTION(&$navigator, &$restriction) {
	return new DateTimeInput($restriction);
	//return new Input($restriction);
}

function &getComponentFor_OBJECTSNAVIGATOR_INDEXRESTRICTION(&$navigator, &$restriction) {
	return new Select($restriction, $restriction->getValues());
}

function &getListComponent_OBJECTSNAVIGATOR_COLLECTION(&$navigator, &$collection) {
	return new CollectionViewer($collection);
}
*/



class Restriction extends ValueHolder {
	var $fieldname;
	var $operation;
	var $name;

	function Restriction($params) {
		$this->name = @$params['name'];
		$this->fieldname = @$params['field'];
		$this->operation = @$params['operation'];
		if ($this->name == null)
			$this->name = $this->fieldname;

		parent::ValueHolder('');
	}

	function printName() {
		return $this->name;
	}

	function applyTo(&$collection) {
		if ($this->isFilled()) {
			//echo 'Setting condition ' . $this->getFieldName() . $this->getOperation() . $this->getSQLValue();
			//$collection->setCondition($this->getFieldName(), $this->getOperation(), $this->getSQLValue());
			$c =& new Condition(array(
				'exp1'=>$this->getFieldName(),
				'operation'=>$this->getOperation(),
				'exp2'=>new ValueExpression($this->getSQLValue())
			));
			$collection->setPathCondition($c);
		}
	}

	function &getName() {
		return $this->name;
	}

	function &getFieldName() {
		return $this->fieldname;
	}

	function &getOperation() {
		return $this->operation;
	}

	function &getSQLValue() {
		return $this->getValue();
	}

	function validate() {
		// Change this
		// We need a field to validate
		return true;
	}

	/*
	function setValue(&$value) {
		print_backtrace($value);
		parent::setValue($value);
	}
	*/
}

class TextRestriction extends Restriction {
	function isFilled() {
		return $this->getValue() !== '';
	}

	function getSQLValue() {
		return '\'' . $this->getValue() . '\'';
	}
}

class WildcardTextRestriction extends Restriction {
	function isFilled() {
		return $this->getValue() !== '';
	}

	function getSQLValue() {
		$v = $this->getValue();
		$vs = explode(' ',$v);
        $vv=array();
        foreach (array_keys($vs) as $i) {
        	$vv[$i] = '\'%' . trim($vs[$i]) . '%\'';
        }

		return implode(' AND '.$this->name .' LIKE ',$vv);
	}
}

class DateRestriction extends TextRestriction {}
class BoolRestriction extends Restriction {
	function isFilled() {
		return $this->getValue() !== false;
	}

	function getSQLValue() {
		if ($this->getValue()) {
			return '1';
		}
		else {
			return '0';
		}
	}
}

class StrictBoolRestriction extends BoolRestriction {
	function isFilled() {
		return true;
	}
}

function &getComponentFor_OBJECTSNAVIGATOR_STRICTBOOLRESTRICTION(&$navigator, &$restriction) {
	return new Checkbox($restriction);
}


#@defmdf getFieldRestrictionComponent[Component](&$field: IndexField)
{
	return new OptionalRestriction(new IndexRestriction(
    			array('values'=>new PersistentCollection($field->getDataType()),'name' => $field->getName(), 'field' => new AttrPathExpression('',$field->getName()))));
}//@#

class IndexRestriction extends Restriction {
	var $values;

	function IndexRestriction($params) {
		$this->values =& $params['values'];
		parent::Restriction($params);
	}
	function isFilled(){
		return true;
	}

	function applyTo(&$collection) {
		if ($this->isFilled()) {
			$c =& new Condition(array(
				'exp1'=>$this->getFieldName(),
				'operation'=>'=',
				'exp2'=>new ObjectExpression($this->getValue())
			));
			$collection->setPathCondition($c);
		}
	}

	function &getValues() {
		return $this->values;
	}
}

class OptionalRestriction extends Restriction{
	function OptionalRestriction(&$restriction){
		$this->res=& $restriction;
		parent::Restriction(array('name'=>$restriction->name, 'field'=>$restriction->fieldname));
	}
	function applyTo(&$collection) {
		if ($this->isFilled()){
			$this->res->applyTo($collection);
		}
	}
	function isFilled(){
		return $this->oc->getSelectOption();
	}
	function setComponent(&$oc){
		$this->oc =& $oc;
	}
}

#@defmdf getComponentFor(&$navigator : ObjectsNavigator, &$restriction : OptionalRestriction)
{
	$oc =& new OptionalComponent(mdcall('getComponentFor',array($navigator, $restriction->res)));
	$restriction->setComponent($oc);
	return $oc;
}//@#


#@defmdf getComponentFor(&$navigator : ObjectsNavigator, &$restriction : IndexRestriction)
{
	$sel =& new Select($restriction, $restriction->getValues());
	return $sel;
}//@#

?>