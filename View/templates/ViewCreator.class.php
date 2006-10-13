<?php

class ViewCreator {
	var $templates = array();
	var $app;
	function ViewCreator(&$app){
		$this->app =& $app;
		$this->templates =& new Collection;
	}
	function reloadView(){
		$app =& $this->app;
		$v=&$app->component->view;
		$app->wholeView->removeChild($v);
		$app->component->releaseView();
		$this->templates =& new Collection;
		$app->translators = array();
		$app->loadTemplates();
		$app->component->createViews();
	}
	function parseTemplates ($files, $templatesdir){
		$p =& new XMLParser;
		$xs = array();
		foreach($files as $f){
			$x = file_get_contents($f);
			$x = str_replace('$templatesdir', $templatesdir, $x);

			foreach ($this->metaVars() as $var => $value) {
				$x = str_replace('$' . $var, $value, $x);
			}

			$xml =& $p->parse($x,$f);
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

	function metaVars() {
		return array('pwbdir' => pwbdir, 'basedir' => basedir, 'site_url' => site_url, 'pwb_url' => pwb_url);
	}

	function loadTemplatesDir ($templatesdir){
		$fs = getfilesrec($lam = lambda('$file','$v=substr($file, -4)=="'.$this->app->page_renderer->templateExtension().'"; return $v;', $a=array()), $templatesdir);
		delete_lambda($lam);
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
		$debugging = false;
		//if (!in_array(strtolower('childrenWithId'),get_class_methods(getClass($parentView)))) print_backtrace(getClass($parentView));
		if ($parentView === null) print_backtrace('The component '.getClass($component).' '.$component->getId().' has no parent view');
		$vid =& $parentView->childrenWithId($id);
		if ($vid!=null){
			if (!$vid->isContainer()){
				$vid->getTemplatesAndContainers();
				$this->instantiateFor($vid,$component);
				if (defined('debugview') and constant('debugview')=='1')$vid->addCSSClass('containerWithId');
				$this->addTemplateName($vid, 'Element:'.getClass($component).'('.$id.')');
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
				if (defined('debugview') and constant('debugview')=='1') {
					$debugging = true;
					$pos =& new HTMLContainer;
					$parentView->appendChild($pos);
				} else {
					$v =& new NullView;
					$this->instantiateFor($v,$component);
					return $v;
				}
			}
		}
		if (!$hasView) {
			$tp0 =& $parentView->templateForClass($component);
			if ($tp0!=null){
				$tp =& $this->instantiateFor($tp0,$component);
				$this->addTemplateName($tp, 'Local:'.$tp0->getAttribute('class').'('.getClass($component).':'.$component->getSimpleId().')');
			} else {
				$tp =& $this->createTemplate($component);
			}
			if (defined('debugview') and 	constant('debugview')=='1') {
				$tp->addCSSClass('template');
			}
			$view =& $tp;
			$view->getTemplatesAndContainers();
			if ($debugging) $view->addCSSClass('debugging');
		}
		$parentView->replaceChild($view,$pos);
		return $view;
	}
	function addTemplateName(&$view, $name){
		if (defined('debugview') and constant('debugview')=='1') {
			$this->app->page_renderer->addTemplateName(&$view, $name);
		}
	}
	function &createTemplate(&$component){
		return $this->templateForClass($component);
	}
	function &templateForClass(&$component){
		$ts =& $this->templates->filter($f = lambda(
				'&$t','$v=$t->isTemplateForClass($component);return $v;',get_defined_vars()));
		delete_lambda($f);

		if (!$ts->isEmpty()) {
			$t = $ts->first();
			$es = $ts->elements();
			foreach(array_keys($es) as $k){
				$tt =& $es[$k];
				if (in_array($t->getClass(), get_superclasses($tt->getClass()))) {
					$t = $tt;
				}
			}
			$v =& $this->instantiateFor($t,$component);
			$this->addTemplateName($v, 'Global:'.$t->getAttribute('class').'('.getClass($component).':'.$component->getSimpleId().')');
			return $v;
		}
		else {
			return $this->defaultTemplate($component);
		}
	}
	function &instantiateFor(&$template, &$component){
		$h = $template->getAttribute('handler');
		if ($h != '' && class_exists($h)){
			$handler =& new $h;
			$handler->setComponent($component);
		} else {
			$vh =& $this->app->page_renderer->viewHandler();
			$handler =& $vh->createFor($component);
		}
		return $handler->instantiateTemplate($template);
	}
	function &defaultTemplate(&$component){
		$vh =& $this->app->page_renderer->viewHandler();
		$handler =& $vh->createFor($component);
		$v =& $handler->defaultView();
		$this->addTemplateName($v, 'Default:'.getClass($component).'('.$component->getSimpleId().')');
		return $v;
	}
}
?>