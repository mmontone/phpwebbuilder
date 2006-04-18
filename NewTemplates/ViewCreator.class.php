<?php

class ViewCreator {
	var $templates = array();
	var $app;
	function ViewCreator(&$app){
		$this->app =& $app;
	}
	function setTemplates($templates){
		$this->templates =& $templates;
	}
	function &createView(&$parentView, &$component){
		$view =& $this->createElemView($parentView, $component);
		$ks = array_keys($component->__children); 
		foreach ($ks as $k){
			$this->createView($view, $component->$k);
		}
		return $view;
	}
	function &createElemView2(&$parentView, &$component){
		$view  =& $component->view;
		if ($view!=null){
			return $view; 
		} else {
			$v =& $this->createTemplate($component);
			$parentView->append_child($v);
			return $v;
		}
	}
	function &createElemView(&$parentView, &$component){
		/*
		 * - If my view is in the parent, return it.
		 * - If there is one, but there's no position, then position it
		 * - Else create the view and position it.
		 * */
		$view  =& $component->view;
		$hasView = $view!=null;
		if ($hasView){
			if ($view->parent != null) return $view; 
		}
		$vids = $parentView->childrenWithId(
				$component->getSimpleId()
			);
		if (count($vids)>0){
			$vid =& array_pop($vids);
			if (!$vid->isContainer()){
				$component->setView($vid);
				return $vid;
			} else {
				$pos =& $vid;
			}
		} else {
		/*	$cts = $parentView->containersForClass(get_class($component));
			if (count($cts)>0){
				$ct =& array_shift($cts);
				$pos =& $ct->createCopy();
				$parentView->insertBefore($ct, $pos);
			} else {
				return new NullView;
			}*/
		}
		if (!$hasView) {
			/*$tps =& $parentView->templatesForClass(get_class($component));
			if (count($tps)>0){
				$tp0 =& array_shift($tps);
				$tp =& $tp0->instantiateFor($component);
			} else {*/
				$tp =& $this->createTemplate($component);
			//}
			$view =& $tp;
		}
		//$parentView->replace_child($pos, $view);
		$parentView->append_child($view);
		return $view;
	}
	function &createTemplate(&$component){
		$tps =& $this->templatesForClass($component);
		$tp = array_shift($tps);
		return $tp->instantiateFor($component);
	}
	function &templatesForClass(&$component){
		$res = array();
		$res = array_filter($this->templates, 
			create_function('$t', 
				'return $t->isTemplateForClass("'.get_class($component).'");'
				)
			);
		array_push($res, $this->defaultTemplate($component));
		return $res;
	}
	function &defaultTemplate(&$component){
		return $component->createDefaultView();
	}
}
?>