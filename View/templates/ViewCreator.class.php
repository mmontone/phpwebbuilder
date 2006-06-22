<?php

class ViewCreator {
	var $templates = array();
	var $app;
	function ViewCreator(&$app){
		$this->app =& $app;
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
		$this->setTemplates($tps);
	}
	function loadTemplatesDir ($templatesdir){
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

	function setTemplates(&$templates){
		$this->templates =& $templates;
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
		if ($hasView){
			if ($view->parentNode != null && $view->parentNode->is($parentView)) return $view;
		}
		$id = $component->getSimpleId();
		$vid =& $parentView->childrenWithId($id);
		if ($vid!=null){
			if (!$vid->isContainer()){
				$vid->getTemplatesAndContainers();
				$component->setView($vid);
				return $vid;
			} else {
				$pos =& $vid;
				$parentView =& $vid->parentNode;
			}
		} else {
			$ct =& $parentView->containerForClass($component);
			if ($ct!=null){
				$parentView =& $ct->parentNode;
				$pos =& $ct->createCopy();
				$parentView->insertBefore($ct, $pos);
			} else {
				$v =& new NullView;
				$component->setView($v);
				return $v;
			}
		}
		if (!$hasView) {
			$tp0 =& $parentView->templateForClass($component);
			if ($tp0!=null){
				$tp =& $tp0->instantiateFor($component);
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
		$ks = array_keys ($this->templates);
		foreach ($ks as $k){
			$t =& $this->templates[$k];
			if ($t->isTemplateForClass($component)){
				return $t;
			}
		}
		return $this->defaultTemplate($component);
	}
	function &defaultTemplate(&$component){
		return $component->createDefaultView();
	}
}
?>