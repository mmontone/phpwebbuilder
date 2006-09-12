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
		$this->checkApp($this->name, $this->app, $this->iters);
	}
	function checkApp($name,&$app, $iters){
		$ad =& new ActionDispatcher();
		$t =& new QKTest();
		$t->name->setValue($name);
		$t->totalPasses->setValue($iters);
		$t->passed->setValue(false);
		if (!$t->save()) {echo DB::lastError();}
		$_SESSION['QKtest'] =& $t;
		for($i=0; $i<$iters; $i++){
		$app->component->view->flushModifications();
			echo "|";
			$ws =& $app->getWidgets();
			$data = array_merge($this->createDispatch($ws), $this->createEvent($ws));
			$p =& new QKPass();
			$p->number->setValue($i);
			$p->test->setTarget($t);
			$p->parameters->setValue(print_r($data, TRUE));
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