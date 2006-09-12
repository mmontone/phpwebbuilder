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
		$tl->setCondition('qkpass.number','>=',' ALL (SELECT number FROM qkpass q WHERE q.test = qkpass.test)');
		$tl->orderBy('timeStarted', 'DESC');
		$tl->setCondition('passed','=', '0');
		$cv = & new TestCollectionViewer($tl);
		$this->addComponent($cv);
		$cv->pageSize->setValue(3);
		$this->addComponent(new CommandLink(array (
			'text' => 'Delete All tests',
			'proceedFunction' => new FunctionObject($this,'deleteTests')
		)));
	}
	function deleteTests() {
		$pl = & new PersistentCollection('QKpass');
		$tl = & new PersistentCollection('QKtest');
		$pl->map(lambda('&$t', '$t->delete()'));
		$tl->map(lambda('&$t', '$t->delete()'));
		$this->deleteTests();
	}
}

class TestCollectionViewer extends CollectionViewer {
	function &addLine(&$e){
		$fc =& parent::addLine(&$e);
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