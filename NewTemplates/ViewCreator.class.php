<?php

class ViewCreator {
	var $templates = array();
	var $app;
	function ViewCreator(&$app){
		$this->app =& $app;
	}
	function parseTemplates ($files){
		$p =& new XMLParser;
		$xs = array();
		foreach($files as $f){
			$x = file_get_contents($f);
			$xml =& $p->parse($x);
			if (!$xml->childNodes) echo $f;
			$ks = array_keys($xml->childNodes); 
			foreach($ks as $k){
				$xs []=& $xml->childNodes[$k];
			}
		}
		$tps = array();
		$tpr =& new HTMLTemplate(); 
		foreach($xs as $x){
			$tps[]=&$tpr->xml2template($x);
		}
		$this->setTemplates($tps);
	}
	function setTemplates(&$templates){
		$this->templates =& $templates;
	}
	function &createView(&$parentView, &$component){
		$view =& $this->createElemView($parentView, $component);
		$ks = array_keys($component->__children);
		$temp = array(); 
		foreach ($ks as $k){
			$this->createView($view, $component->$k);
		}
		return $view;
	}
	function &createElemView(&$pV, &$component){
		/*
		 * - If my view is in the parent, return it.
		 * - If there is one, but there's no position, then position it
		 * - Else create the view and position it.
		 * */
		$parentView =& $pV;
		$view  =& $component->view;
		$hasView = $view!=null && strcasecmp(get_class($view),"NullView")!=0;
		if ($hasView){
			if ($view->parent != null) return $view; 
		}
		$id = $component->getSimpleId();
		$vids = $parentView->childrenWithId(
				$id
			);
		if (count($vids)>0){
			$vid =& $vids[0];
			if (!$vid->isContainer()){
				$component->setView($vid);
				return $vid;
			} else {
				$pos =& $vid;
				$parentView =& $vid->parent;
			}
		} else {
			$cts =& $parentView->containersForClass($component);
			if (count($cts)>0){
				$ct =& $cts[0];
				$parentView =& $ct->parent; 
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
		}
		$parentView->replace_child($pos, $view);
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