<?php

class DBUpdater{
	var $last_updated;
	function &Instance(){
		$dbu =& getSessionGlobal('DBUpdates');
		if ($dbu==null){
			$dbu = new DBUpdater;
			$dbu->last_updated=PWBDateTime::format(getDate());
		}
		return $dbu;
	}
	function updateAll(){
		$dbu =& DBUpdater::Instance();
		//$dbu->doUpdateAll();
	}

    function doUpdateAll(){
    	$time = "'".$this->last_updated."'";
    	$rep =& #@select u:DBUpdate where last_updated > $time@#;

    	$rep->refresh();
    	foreach($rep->elements() as $t){
    		$class = $t->tableName->getValue();
    		$objs =& $GLOBALS['persistentObjects'][$class];
    		if ($objs==null) continue;
    		$oe =& new OrExp;
    		foreach(@array_keys($objs) as $k){
    			$oe->addExpression(new EqualCondition(array('exp1'=>new ObjectPathExpression(''),'exp2'=>new ValueExpression($objs[$k]->getId()))));
    		}
    		$r =& new Report(array('class'=>$class, 'exp'=>&$oe));
    		$r->elements();
    	}

    	$this->last_updated=PWBDateTime::format(getDate());
    }
    function markUpdated($table){
		return;
		$dbu =& DBUpdate::getWithIndex('DBUpdate' ,array('tableName'=>"'$table'"));
		if ($dbu==null){
			$dbu =& new DBUpdate();
			$dbu->tableName->setValue($table);
		}
		$dbu->last_updated->setNow();
		$dbu->save();
    }
}
class DBUpdate extends PersistentObject{
    function initialize() {
    	$this->addField(new TextField(array('fieldName'=>'tableName', 'is_index'=>true)));
    	$this->addField(new DateTimeField(array('fieldName'=>'last_updated')));
    }
	function markAsUpdated(){}
}
?>