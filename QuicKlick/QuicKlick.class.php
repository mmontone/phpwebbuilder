<?php

class QuicKlick {
	function QuicKlick($name, $funCode, $iters, $clicks){
		$this->name = $name;
		$this->iters = $clicks;
		$dateFormat = 'Y-m-d H:i:s';
		$_SESSION['QuicKlick']=&$this;
		$loc =pwb_url.'/QuicKlick/runqk.php' .
				'?app_class='. app_class.
				'&basedir='.basedir .
				'&sid='.session_id();
		echo "<h1>$name starting at ".date($dateFormat)."</h1>";
		$errors=0;
		$fun =& QKFunction::getWithIndex('QKFunction',array('name'=>$name));
		if ($fun==false){
			$fun =& new QKFunction;
			$fun->code->setValue($funCode);
			$fun->name->setValue($name);
			$fun->save();
		}
		$f = $fun->getFun();

		for($i=0; $i<$iters; $i++){
			$this->app=&$f();
			if (!is_a($this->app, 'Application')) {
				echo getClass($this->app)." Cannot be tested";
				exit;
			}
			session_write_close();
			SessionHandler::setHooks();
			$res =  file_get_contents($loc);
			session_start();
			$t =& $_SESSION['QKtest'];
			$t->function->setTarget($fun);
			$t->save();
			if (!$t->passed->getValue()){
				echo "error ".$res;
				$p =& $t->lastPass();
				$p->output->setValue(addslashes($res));
				$p->save();
				$errors++;
			} else echo $res;
			echo "test $i ended at ".date($dateFormat);
			echo "<hr/>";
		}
		echo "<script>document.title='Finished - $errors errors';</script>";
	}
	function check(){
		$this->checkApp($this->name, $this->iters);
	}
	function getSendable(){
			$ws =& $this->app->getWidgets();
			return array_merge($this->createDispatch($ws), $this->createEvent($ws));
	}
	function checkApp($name, $iters){
		$ad =& new ActionDispatcher();
		$t =& new QKTest();
		$t->name->setValue($name);
		$t->totalPasses->setValue($iters);
		$t->passed->setValue(false);
		if (!$t->save()) {echo DBSession::lastError();}
		$_SESSION['QKtest'] =& $t;
		for($i=0; $i<$iters; $i++){
			//$this->app->component->view->flushModifications();
			$data = $this->getSendable($i);
			$p =& new QKPass();
			$p->number->setValue($i);
			$p->test->setTarget($t);
			$p->parameters->setValue(serialize($data));
			$p->save();
			ob_start();
			$ad->dispatchData($data);
			$res = ob_get_clean();
			$p->output->setValue(addslashes($res));
			echo $res;
			if (!$p->save()) {echo DBSession::lastError();}
		}
		$t->timeEnded->setNow();
		$t->passed->setValue(true);
		$t->save();

	}
	function createDispatch($widgets){
		$ig =& new InputGenerator();
		$mods = array();
		foreach($widgets as $id=>$w) {
			$m = $ig->createFor($w);
			if ($m!==null)$mods[$id]=$m;
		}
		return $mods;
	}
	function createEvent($widgets){
		$evs = array('changed', 'blur', 'focus',  'click');
		foreach ($widgets as $wid=>$w){
			foreach ($evs as $e){
				if (isset($w->event_listeners[$e])){
					$ret []= array('event_target'=>$wid, 'event'=>$e);
				}
			}
		}
		$ev =& array_rand($ret);
		return $ret[$ev];
	}
}

class QuicKlickReprise extends QuicKlick{
	function QuicKlickReprise(&$test){
		$_SESSION['QuicKlick']=&$this;
		$this->iters = $test->passes->collection->size();
		$this->name = $test->name->getValue();
		$this->test =& $test;
		$loc =pwb_url.'/QuicKlick/runqk.php' .
				'?app_class='. app_class.
				'&basedir='.basedir .
				'&sid='.session_id();
		$funOb =& $test->function->getTarget();
		$fun =& $funOb->getFun();
		$this->app=&$fun();
		$this->check();
		$t =& $_SESSION['QKtest'];
		if ($t===null) return;
		if (!$t->passed->getValue()){
			$p =& $t->lastPass();
			$p->output->setValue('');
			$p->save();
		}
		$t->function->setTarget($funOb);
		$t->save();
	}
	function getSendable($i){
		$col =& $this->test->passes->collection;
		$col->setCondition('number','=',$i);
		$p =& $col->first();
		$col->removeCondition('number');
		return unserialize($p->parameters->getValue());
	}
}



?>