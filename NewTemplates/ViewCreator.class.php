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
/*	function &createView(&$parentView, &$component){
		$view =& $this->createElemView($parentView, $component);
		$ks = array_keys($component->__children);
		foreach ($ks as $k){
			$this->createView($view, $component->componentAt($k));
		}
		return $view;
	}*/

	function &createElemView(&$pV, &$component){
		/*
		 * - If my view is in the parent, return it.
		 * - If there is one, but there's no position, then position it
		 * - Else create the view and position it.
		 * */
		$parentView =& $pV;
		$view  =& $component->view;
		$hasView = $view!=null && strcasecmp(get_class($view),'NullView')!=0;
		if ($hasView){
			if ($view->parentNode != null && $view->parentNode->getRealId() == $parentView->getRealId()) return $view;
		}
		$id = $component->getSimpleId();
		$vid =& $parentView->childrenWithId($id);
		if ($vid!=null){
			if (!$vid->isContainer()){
				$component->setView($vid);
				return $vid;
			} else {
				$pos =& $vid;
				$parentView =& $vid->parentNode;
			}
		} else {
			$cts =& $parentView->containersForClass($component);
			if (count($cts)>0){
				$ct =& $cts[0];
				$parentView =& $ct->parentNode;
				$pos =& $ct->createCopy();
				$parentView->insert_before($ct, $pos);
			} else {
				$v =& new NullView;
				$component->setView($v);
				return $v;
			}
		}
		if (!$hasView) {
			$tps =& $parentView->templatesForClass($component);
			if (count($tps)>0){
				$tp0 =& $tps[0];
				$tp =& $tp0->instantiateFor($component);
			} else {
				$tp =& $this->createTemplate($component);
			}
			$view =& $tp;
			$view->getTemplatesAndContainers();
		}
		$parentView->replace_child($view,$pos);
		return $view;
	}
	function &createTemplate(&$component){
		$tps =& $this->templatesForClass($component);
		$tp =& $tps[0];
		return $tp->instantiateFor($component);
	}
	function &templatesForClass(&$component){
		$res = array();
		$ks = array_keys ($this->templates);
		foreach ($ks as $k){
			$t =& $this->templates[$k];
			if ($t->isTemplateForClass($component)){
				$res[]=&$t;
			}
		}
		$res[]=& $this->defaultTemplate($component);
		return $res;
	}
	function &defaultTemplate(&$component){
		return $component->createDefaultView();
	}
}
?>