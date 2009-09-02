<?php

class ActionDispatcher {
	function sendData($data){
		session_name(strtolower($data['app']));
		$file = ini_get('session.save_path').'/'.$data['app'].'-'.$data[$data['app']].'.cmt';
		touch($file);
		return (msg_send(msg_get_queue(ftok($file, 'c')),
			1,$data,true,FALSE,$ec));
	}
	function & dispatch() {
		$ad =& new ActionDispatcher;
		$form = array_merge($_REQUEST, $_FILES);
		return $ad->dispatchData($form);
	}
	function &initializeComet(){
		$ad =& new ActionDispatcher;
		$ad->file = ini_get('session.save_path').'/'.session_name().'-'.session_id().'.cmt';
		msg_remove_queue(msg_get_queue(ftok($ad->file, 'c')));
		return $ad;
	}
	function dispatchComet(){
		#@profile xdebug_start_profiling();@#
		//echo 'waiting...';flush();
		$type = $params = 0;
		touch($this->file);
		msg_receive(msg_get_queue(ftok($this->file, 'c')),
			1,$type, 2048, $params);
		$win =& $this->dispatchData($params);
		if ($params['showStopLoading'])$win->showStopLoading();
		$this->params = $params;
		//print_r($params);
		return isset($params['showStopLoading']);
	}
	function &dispatchData($form){
		$event = array ();
		$view_updates = array ();
		$de = 0;
		$app = & Application::instance();

		$event['window'] =&$app->windows['root'];
		$event['app'] = & $app;
		foreach ($form as $dir => $param) {
			switch ($dir) {
				case 'event' :
					$event['event'] = $param;
					break;
				case 'event_target' :
					$event['target'] = $param;
					break;
				case 'app' :
					break;
				case 'window':
					$event['window'] =& $app->windows[$param];
					#@typecheck $event['window']:Window@#
					break;
				default :
					$c = & $this->getComponent($dir, $app);
					if ($c != null) {
						$temp = & $view_updates[$de++];
						$temp[] = & $c;
						$temp[] = utf8_decode($param);
						$temp[] = $dir;
					}
			}
		}

        $target = & $this->getComponent(@$event['target'], $event['app']);
        //echo 'Triggering event. Target: ' . $event['target'] . '.Event: ' . $event['event'] . '</br>';
        //var_dump(getClass($target));
        DBSession::Instance()->prepareForModification();
        Window::setActiveInstance($event['window']);
		$this->updateViews($view_updates);
		$this->triggerEvent($event);
		if (isset($form['bm'])) {$event['window']->goToUrl($form['bm']);}
        DBUpdater::updateAll();
		EventHandler::ExecuteDeferredEvents();

        #@track_events
        global $triggeredEvents;
        echo $triggeredEvents . ' events triggered in total<br/>';
        //@#
		return $event['window'];
	}


	function updateViews(& $updates) {
		$ks = array_keys($updates);
		foreach ($ks as $k) {
			//echo 'View updated: ' . getClass($updates[$k][0]) . $updates[$k][0]->getId() . ' update: ' . $updates[$k][1] . '</br>';
			//$updates[$k][0]->viewUpdated($updates[$k][1]);

            // We use a FunctionObject here so that the dynamic variable 'current_component''
            // is set. That is needed in order to be able to register model changes in the memory transaction.
            // The current transaction is set in Component>>aboutToExecuteFunction method. See MemoryTransaction implementation
            //                                     -- marian
            try{
	            $f =& new FunctionObject($updates[$k][0], 'viewUpdated');
	            $f->executeWith($updates[$k][1]);
            } catch(Exception $e){
            	$updates[$k][0]->updatingException($e, $updates[$k][1]);
            } catch(Error $e){
            	$updates[$k][0]->updatingException($e, $updates[$k][1]);
            }
		}
	}
	function triggerEvent(& $event) {
		$target = & $this->getComponent(@$event['target'], $event['app']);
		//echo 'Triggering event. Target: ' . $event['target'] . '.Event: ' . $event['event'] . '</br>';
		//var_dump(getClass($target));
		if ($target!=null){
			$target->triggerEvent($event['event'], $v = array ());
		}
	}
	function & getComponent($path, & $app) {
		if ($path=='app') return $app;
		$path = explode(CHILD_SEPARATOR, $path);
		if ($path[0] == "app") { // Maybe the parameter wasn't for us'
			$comp = & $app->windows[$path[2]]->component;
			array_shift($path);
			array_shift($path);
			array_shift($path);
			foreach ($path as $p) {
				$comp1 = & $comp->componentAt($p);
				if ($comp1 == null) {
					$comp->redraw(); //We sent something to a thing that wasn't there. We render the parent to see what's really there.
					return $comp1;
				}
				$comp = & $comp1;
			}
			return $comp;
		}
		else {
			$n = null;
			return $n;
		}
	}
}
?>