<?php

class ViewCreator {
	var $templates = array();
	var $app;
	function ViewCreator(&$app){
		$this->app =& $app;
		$this->templates =& new Collection;
	}
	function parseTemplates ($files, $templatesdir){
		$p =& new XMLParser;
		$xs = array();
		foreach($files as $f){
			$x = file_get_contents($f);
			$x2 = str_replace('$templatesdir', $templatesdir, $x);
			$xml =& $p->parse($x2);
			$cm =& $xml->childNodes;
			$ks = array_keys($cm);
			foreach($ks as $k){
				$xs []=& $cm[$k];
			}
		}
		$tps = array();
		$tpr =& new HTMLTemplate();
		foreach($xs as $x){
			$tps[]=&$tpr->xml2template($x);
		}
		$this->addTemplates($tps);
	}
	function loadTemplatesDir ($templatesdir){
		if (!file_exists($templatesdir)) return;
 		$gestor=opendir($templatesdir);
 		$fs = array();
		while (false !== ($f = readdir($gestor))) {
			if (substr($f, -4)=='.xml'){
				$fs []= $templatesdir."/".$f;
			}
		}
		$size = strlen(pwbdir);
		if (pwbdir == substr ($templatesdir, 0, $size)){
			$temp_url = pwb_url. substr ($templatesdir, $size);
		} else {
			$temp_url = site_url . substr ($templatesdir, strlen(basedir));
		}
 		$this->parseTemplates($fs, $temp_url);
 	}

	function addTemplates(&$templates){
		$this->templates->addAll($templates);
	}

	function createAllViews(){
		$nv =& $this->app->needView;
		$ks = array_keys($nv);
		foreach($ks as $k){
			$c =& $nv[$k];
			$this->createElemView($c->parentView(), $c);
		}
		foreach($ks as $k){
			$c =& $nv[$k];
			$c->prepareToRender();
		}
		$this->app->needView = array();
	}

	function &createElemView(&$pV, &$component){
		/*
		 * - If my view is in the parent, return it.
		 * - If there is one, but there's no position, then position it
		 * - Else create the view and position it.
		 * */
		$parentView =& $pV;
		$view  =& $component->view;
		$hasView = $view!=null && strcasecmp(getClass($view),'NullView')!=0 && strcasecmp(getClass($view),'StdClass')!=0;
		//Somewhere er're setting a value in view where the view is not initialized;
		//if (strcasecmp(getClass($view),'StdClass')==0) print_backtrace(get_class($component));
		$id = $component->getSimpleId();
		if (!in_array(strtolower('childrenWithId'),get_class_methods(getClass($parentView)))) print_backtrace(getClass($parentView));
		$vid =& $parentView->childrenWithId($id);
		if ($vid!=null){
			if (!$vid->isContainer()){
				$vid->getTemplatesAndContainers();
				$component->setView($vid);
				trigger_error('Component '.$id.' ('.getClass($component).') of '.$component->holder->parent->getId() . ' got element '.$vid->tagName,E_USER_NOTICE);
				return $vid;
			} else {
				$pos =& $vid;
				trigger_error('Component '.$id.' ('.getClass($component).') of '.$component->holder->parent->getId() . ' found container with id',E_USER_NOTICE);
				$parentView =& $vid->parentNode;
			}
		} else {
			$ct =& $parentView->containerForClass($component);
			if ($ct!=null){
				$parentView =& $ct->parentNode;
				$pos =& $ct->createCopy();
				$parentView->insertBefore($ct, $pos);
				trigger_error('Component '.$id.' ('.getClass($component).') of '.$component->getId() . ' found container for class '.$ct->getAttribute('class'),E_USER_NOTICE);
			} else {
				$v =& new NullView;
				$component->setView($v);
				trigger_error('Component '.$id.' ('.getClass($component).') of '.$component->getId() . ' got NullView ',E_USER_NOTICE);
				return $v;
			}
		}
		if (!$hasView) {
			$tp0 =& $parentView->templateForClass($component);
			if ($tp0!=null){
				$tp =& $tp0->instantiateFor($component);
				trigger_error('Component '.$id.' ('.getClass($component).') of '.$component->getId() . ' gets Local Template for class '.$tp0->getAttribute('class'),E_USER_NOTICE);
			} else {
				$tp =& $this->createTemplate($component);
			}
			$view =& $tp;
			$view->getTemplatesAndContainers();
		}
		$parentView->replaceChild($view,$pos);
		return $view;
	}
	function &createTemplate(&$component){
		$tp =& $this->templateForClass($component);
		return $tp->instantiateFor($component);
	}
	function &templateForClass(&$component){
		$ts =& $this->templates->filter($f = lambda(
				'&$t','return $t->isTemplateForClass($component);',get_defined_vars()));
		delete_lambda($f);
		if (!$ts->isEmpty()){
			$t =& $ts->first();
			trigger_error('Component '.$component->getSimpleId().' ('.getClass($component).') of '.$component->getId() . ' gets Global Template for class '.$t->getAttribute('class'),E_USER_NOTICE);
			return $t;
		}
		return $this->defaultTemplate($component);
	}
	function &defaultTemplate(&$component){
		trigger_error('Component '.$component->getSimpleId().' ('.getClass($component).') of '.$component->getId() . ' gets Default Template',E_USER_NOTICE);
		return $component->createDefaultView();
	}
}
?>