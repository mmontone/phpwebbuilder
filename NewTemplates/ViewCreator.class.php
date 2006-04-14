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
/*- Se crea una vista para cada hijo.*/
		$view =& $this->createElemView($parentView, $component);
		$ks = array_keys($component->__children); 
		foreach ($ks as $k){
			$this->createView($view, $component->$k);
		}
	}
	function &createElemView(&$parentView, &$component){

		/* First: Check if there's already an item with our id 
		 */

		$ids =& $parentView->childrenWithId($component->getSimpleId());
		if (count($ids)>0){
			if (!$ids[0]->isContainer()){
				$component->setView($ids[0]);
				return $ids[0];
			} else {
				$view =& $this->createTemplate($component);
				$parentView->replace_child($ids[0], $view);
				return $view;
			}
		}
	/*- Existe un template para la clase del elemento dentro del template
	actual. Se agrega el template en esa misma posici?n dentro de la clase
	padre (?til para colecciones. caso "MenuItem" en Menu).*/
		$tps =& $parentView->templatesForClass(get_class($component));
		if (count($tps)>0){
			$view =& $tps[0]->instantiateFor($component);
			$parentView->insertBefore($tps[0], $view);
			return $view;
		}
		$view =& $this->createTemplate($component);
		$parentView->append_child($view);
		return $view;
	}
/*- Existe un template global para el elemento. se instancia y se agrega
dentro del template del padre.*/
	function &createTemplate(&$component){
		$tps =& $this->templatesForClass($component);
		return  $tps[0]->instantiateFor($component);
/*- Se crea una vista default para el elemento.*/
	}
	function &templatesForClass(&$component){
		return array($this->defaultTemplate($component));
	}
	function &defaultTemplate(&$component){
		return $component->createView();
	}
}
?>