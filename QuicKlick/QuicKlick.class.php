<?php

class QuicKlick {
	function QuicKlick($name, $fun, $iters, $clicks){
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
		for($i=0; $i<$iters; $i++){
			$this->app=&$fun();
			session_write_close();
			$res =  file_get_contents($loc);
			session_start();
			$t =& $_SESSION['QKtest'];
			if ($t===null || !$t->passed->getValue()){
				echo "error ".$res;
				$p =& $t->lastPass();
				$p->output->setValue(addslashes($res));
				$p->save();
				$errors++;
			} else echo $res;
			echo "test $i ended at ".date($dateFormat);
			echo "<hr/>";
		}
		echo "<script>window.title='Finished - $errors errors';</script>";
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
		if (!$t->save()) {echo DB::lastError();}
		$_SESSION['QKtest'] =& $t;
		for($i=0; $i<$iters; $i++){
		$app->component->view->flushModifications();
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
			if (!$p->save()) {echo DB::lastError();}
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
		$this->iters = 1;
		$this->name = $test->name->getValue();
		$this->test =& $test;
		$loc =pwb_url.'/QuicKlick/runqk.php' .
				'?app_class='. app_class.
				'&basedir='.basedir .
				'&sid='.session_id();
		$funOb = $test->function->getTarget();
		$fun = eval($funOb->code->getValue());
		$this->app=&$fun();
		session_write_close();
		$res =  file_get_contents($loc);
		session_start();
		$t =& $_SESSION['QKtest'];
		if ($t===null) return;
		if (!$t->passed->getValue()){
			$p =& $t->lastPass();
			$p->output->setValue(addslashes($res));
			$p->save();
		}
	}
	function getSendable($i){
		$col =& $this->test->passes->collection;
		$col->setCondition('number','=',$i);
		$p =& $col->first();
		return unserialize($p->parameters->getValue());
	}
}

class InputGenerator extends PWBFactory{
	function createInstanceFor(&$widget){
		//return "";
		return null;
	}
}

class InputInputGenerator extends PWBFactory{
	function createInstanceFor(&$widget){
		return "admin";
	}
}

class PasswordInputGenerator extends PWBFactory{
	function createInstanceFor(&$widget){
		return "PWB-Admin";
	}
}


?>