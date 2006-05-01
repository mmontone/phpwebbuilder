<?php
class PageRenderer // extends PWBObject
{
	var $page;
	var $csss = array();

	function PageRenderer(& $page) {
		$this->page = $page;
	}
}

class StandardPageRenderer extends PageRenderer {
	function StandardPageRenderer(& $page) {
		parent :: PageRenderer($page);
	}

	function renderPage() {
		$this->page->tagName='form';
		$this->page->setAttribute('action','new_dispatch.php');
		$this->page->setAttribute('method','POST');
		$this->page->setAttribute('enctype','multipart/form-data');
		$ret='<html>'.
				'<head><script src="'.site_url.'admin/ajax/ajax.js"></script>';
		foreach($this->csss as $c){
			$ret.='<link rel="stylesheet" href="'.$c.'" />';
		}
		$ret.= '</head><body>';
		$ret .= $this->page->render();
		$this->page->flushModifications();
		$ret .='</body></html>';
		return $ret;
	}


	function showXML() {
		$ret = $this->renderPage();
		$ret = str_replace("<", "&lt;", $ret);
		$ret = str_replace(">", "&gt;", $ret);
		$ret = str_replace("\n", "<br/>", $ret);
		$ret = str_replace("   ", "&nbsp;&nbsp;&nbsp;", $ret);
		return $ret;
	}

	function renderActionLinkAction(&$action_link) {
		return 'callAction(&#34;'.$action_link->getId().'&#34;);';
	}
}

class AjaxPageRenderer extends PageRenderer {
	function AjaxRenderer(& $page) {
		parent :: PageRenderer($page);
	}

	function renderPage() {
		header("Content-type: text/xml");
		$xml = '<?xml version="1.0" encoding="ISO-8859-1" ?>';
		$xml .= "\n<ajax_response>";
		$xml .= $this->renderAjaxResponseCommands($this->page);
		$xml .= "</ajax_response>";

		$this->page->flushModifications();

		echo $xml;
		exit;
	}

	function renderAjaxResponseCommands(& $node) {
		$xml = '';
		$look_for_modifications = $node->childNodes;

		foreach ($node->modifications as $modification) {
			$xml .= $this->renderModification($node, $modification, $look_for_modifications);
		}

		foreach ($look_for_modifications as $child) {
			$xml .= $this->renderAjaxResponseCommands($child);
		}

		return $xml;
	}

	function renderModification(&$node, & $mod, & $look_for_modifications) {
		return $mod->visit($this, array (
			'target' => $node,
			'look_for_modifications' => $look_for_modifications
		));
	}

	function visitReplaceChildXMLNodeModification(& $mod, $params) {
		$look_for_modifications = & $params['look_for_modifications'];
		$this->deleteXMLChildNode($mod->child, $look_for_modifications);
		return $mod->renderAjaxResponseCommand($params['target']);
	}

	function visitAppendChildXMLNodeModification(& $mod, $params) {
		$look_for_modifications = & $params['look_for_modifications'];
		$this->deleteXMLChildNode($mod->child, $look_for_modifications);
		return $mod->renderAjaxResponseCommand($params['target']);
	}

	function visitRemoveChildXMLNodeModification(& $mod, $params) {
		return $mod->renderAjaxResponseCommand($params['target']);
	}

	function visitSetAttributeXMLNodeModification(& $mod, $params) {
		return $mod->renderAjaxResponseCommand($params['target']);
	}

	function deleteXMLChildNode(&$node, &$array) {
		$keys = array_keys($array);

		foreach ($keys as $key) {
			$array_elem =& $array[$key];
			// Puedo comparar por parentPosition porque se que ambos son hijos del mismo padre
			if ($elem->parentPosition == $array_elem->parentPosition) {
				unset($array[$key]);
				return true;
			}
		}
		return false;
	}

	function renderActionLinkAction(&$action_link) {
		return 'callActionAjax(&#34;'.$action_link->getId().'&#34;);';
	}
}






?>