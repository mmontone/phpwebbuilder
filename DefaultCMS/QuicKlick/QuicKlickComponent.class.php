<?php
class QuicKlickComponent extends Component {
	function permissionNeeded() {
		return 'DatabaseAdmin';
	}
	function initialize() {
		$tl = & new Report;
		$tl->dataType = 'QKtest';
		$tl->fields = array('qkpass.number'=>'number','qkpass.output'=>'output');
		$tl->tables = array('qktest INNER JOIN qkpass ON qkpass.test=qktest.id');
		$tl->orderBy('timeStarted', 'DESC');
		$cv = & new TestCollectionViewer($tl);
		$this->addComponent($cv, 'tests');
		$cv->pageSize->setValue(3);
		$this->addComponent(new CommandLink(array (
			'text' => 'Delete All tests',
			'proceedFunction' => new FunctionObject($this,'deleteTests')
		)));
		$this->addComponent(new Label('Hide Succeded'));
		$this->addComponent($cb=&new CheckBox(new ValueHolder($v=true)), 'hideSucceded');
		$cb->addInterestIn('changed', new FunctionObject($this,'updateSelection'));
		$this->updateSelection();
	}
	function updateSelection(){
		if ($this->hideSucceded->getValue()){
			$this->tests->col->setCondition('passed','=',  '0');
		} else {
			$this->tests->col->discardConditions();
		}
		$this->tests->col->setCondition('qkpass.number','>=',' ALL (SELECT number FROM qkpass q WHERE q.test = qkpass.test)');
		$this->tests->refresh();
	}
	function deleteTests() {
		$tl = & new PersistentCollection('QKtest');
		$tl->collect('deleteTest()');
		$this->tests->refresh();
	}
}

class TestCollectionViewer extends CollectionViewer {
	function &addLine(&$e){
		$fc =& parent::addLine($e);
		$fc2 = & new FieldValueComponent;
		$fc2->addComponent(new CommandLink(array (
			'text' => 'Volver a Ejecutar',
			'proceedFunction' => new FunctionObject($e,
			'runAgain',
			array (
				'object' => & $e
			)
		))), 'value');
		$fc->addComponent($fc2, 'goAgain');

		$fc2 = & new FieldValueComponent;
		$fc2->addComponent(new CommandLink(array (
			'text' => 'Eliminar',
			'proceedFunction' => new FunctionObject($e,
			'deleteTest',
			array (
				'object' => & $e
			)
		))), 'value');
		$fc->addComponent($fc2, 'remove');
		return $fc;
	}
}

?>