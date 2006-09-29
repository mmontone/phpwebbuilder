<?php
class ActionDispatcher {
	function & dispatch() {
		$form = array_merge($_REQUEST, $_FILES);
		return $this->dispatchData($form);
	}
	function &dispatchData($form){
		$event = array ();
		$view_updates = array ();
		$de = 0;
		$app = & Application::instance();
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
				default :
					$c = & $this->getComponent($dir, $app);
					if ($c != null) {
						$temp = & $view_updates[$de++];
						$temp[] = & $c;
						$temp[] = $param;
						$temp[] = $dir;
					}
			}
		}
		$this->updateViews($view_updates);
		$this->triggerEvent($event);
		if (isset($form['bookmarkchange']))$app->goToUrl($form['bookmarkchange']);
		return $app;
	}
	function updateViews(& $updates) {
		$ks = array_keys($updates);
		foreach ($ks as $k) {
			//echo 'View updated: ' . getClass($updates[$k][0]) . $updates[$k][0]->getId() . ' update: ' . $updates[$k][1] . '</br>';
			$updates[$k][0]->viewUpdated($updates[$k][1]);
		}
	}
	function triggerEvent(& $event) {
		//echo 'Triggering event. Target: ' . $event['target'] . '.Event: ' . $event['event'] . '</br>';
		$target = & $this->getComponent($event['target'], $event['app']);
		if ($target!=null){
			$target->triggerEvent($event['event'], $v = array ());
		}
	}
	function & getComponent($path, & $app) {
		if ($path=='app') return $app;
		$path = explode(CHILD_SEPARATOR, $path);
		if ($path[0] == "app") { // Maybe the parameter wasn't for us'
			$comp = & $app->component;
			array_shift($path);
			array_shift($path);
			array_shift($path);
			foreach ($path as $p) {
				$comp1 = & $comp->componentAt($p);
				if ($comp1 == null) {
					$comp->redraw(); //We sent something to a thing that wasn't there. We render the parent to see what's really there.
					return null;
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