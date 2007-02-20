<?php

class ViewCreator {
	var $app;
	function ViewCreator(&$app){
		$this->app =& $app;
	}
	function reloadView(){
		$app =& $this->app;
		$app->translators = array();

		foreach(array_keys($app->windows) as $wk){
			$win =& $app->windows[$wk];
			$v=&$win->component->view;
			$win->component->reloadView();
			$win->wholeView->removeChilds();
			$win->wholeView->appendChild($win->component->view);
			//$win->redraw();
			//$win->wholeView->replaceChild($win->component->view,$v);
		}

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
		$fs = getfilesrec(lambda('$file','$v=substr($file, -4)=="'.$this->app->page_renderer->templateExtension().'"; return $v;', $a=array()), $templatesdir);

		$size = strlen(pwbdir);
		if (pwbdir == substr ($templatesdir, 0, $size)){
			$temp_url = pwb_url. substr ($templatesdir, $size);
		} else {
			$temp_url = site_url . substr ($templatesdir, strlen(basedir));
		}
 		$this->parseTemplates($fs, $temp_url);
 	}

	function addTemplates(&$templates){
		global $templates_xml;
		$a=array();
		$templates_xml->addTemplatesAndContainers($templates, $a, $a);
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
		$id = $component->getSimpleId();
		//check $parentView !== null
		$vid =& $parentView->childrenWithId($id);
		#@debugview $db = false;@#
		if ($vid!=null){
			if (!$vid->isContainer()){
				$vid->getTemplatesAndContainers();
				$this->instantiateFor($vid,$component);
				#@debugview
					{$vid->addCSSClass('containerWithId');
					$this->addTemplateName($vid, 'Element:'.getClass($component).'('.$id.')');}//@#
				$component->viewHandler->prepareToRender();
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
				#@debugview
				{
					$db = true;
					$pos =& new HTMLContainer;
					$parentView->appendChild($pos);
				} if(false) //@#
				{
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
				#@debugview $this->addTemplateName($tp, 'Local:'.$tp0->getAttribute('class').'('.getClass($component).':'.$component->getSimpleId().')');@#
			} else {
				$tp =& $this->createTemplate($component);
			}
			#@debugview $tp->addCSSClass('template');@#
			$view =& $tp;
			$view->getTemplatesAndContainers();
			#@debugview if ( $db) $view->addCSSClass('debugging');@#
		}
		$parentView->replaceChild($view,$pos);
		$component->viewHandler->prepareToRender();
		return $view;
	}
	function addTemplateName(&$view, $name){
		$this->app->page_renderer->addTemplateName($view, $name);
	}
	function &createTemplate(&$component){
		return $this->templateForClass($component);
	}
	function getTemplatesFilename(){
		$comp =& Compiler::Instance();
		return $comp->getTempDir('').strtolower(constant('app_class')).'-templates.php';
	}
	function &getTemplates(){
		global $templates_xml;
		if ($templates_xml===null) {
			$temp_file = $this->getTemplatesFilename();
			if ((!file_exists($temp_file)) || @constant('templates')=='recompile' || (!(Compiler::CompileOpt('recursive')||Compiler::CompileOpt('optimal')))) {
				$templates_xml = new XMLNode;
				$this->app->loadTemplates();
				if (!(@Constant('templates')=='recompile')){
					$fo = fopen($temp_file, 'w');
					fwrite($fo, serialize($templates_xml));
					fclose($fo);
				}
			} else {
				$templates_xml = unserialize(file_get_contents($temp_file));
			}

		}
		return $templates_xml;
	}
	function &templateForClass(&$component){
		$ts =& $this->getTemplates();
		$t =& $ts->templateForClass($component);
		if ($t!==null) {
			$v =& $this->instantiateFor($t,$component);
			#@debugview $this->addTemplateName($v, 'Global:'.$t->getAttribute('class').'('.getClass($component).':'.$component->getSimpleId().')');@#
			return $v;
		} else {
			return $this->defaultTemplate($component);
		}
	}
	function &instantiateFor(&$template, &$component){
		$h = $template->getAttribute('handler');
		if ($h != null && Compiler::requiredClass($h)){
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
		#@debugview $this->addTemplateName($v, 'Default:'.getClass($component).'('.$component->getSimpleId().')');@#
		return $v;
	}
}
?>