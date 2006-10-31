<?php

require_once 'spyc-0.2.3/spyc.php';
require_once 'qsort.php';

function str_is_a($t1, $t2) {
	if (str_is_primitive($t1)) {
		return $t2 == $t2;
	}
	else {
		$o =& new $t1;
		return is_a($o, $t2);
	}
}

function str_is_primitive($s) {
	return $s == 'array' or $s == 'bool' or $s == 'float' or $s  == 'int' or $s == 'string';
}


class MDDefinitionsMap {
	var $map = array();

	function addDefinition($name, $f) {
		if ($this->map[$name] == null) {
			$this->map[$name] = array();
		}

		$this->_addDefinition($this->map, $name, $f);
	}

	function getFunctionFor(&$call) {
		print_backtrace(getClass($this) . ': Subclass responsibility');
	}

	function &at($key) {
		return $this->map[$key];
	}

	function printHtml() {
		$s = '';
		foreach($this->map as $func => $defs) {
			$s .= 'Function: ' .$func . '<br/>';
			foreach ($defs as $def) {
				$s .= 'Def: ' . print_r($def, true) . '<br/>';
			}
		}
		return $s;
	}
}

class OrdinaryMDDefinitionsMap extends MDDefinitionsMap {
	function _addDefinition(&$map, $name, $f) {
		$map[$name][] =& $f;

		// Improve: insert in order, do not sort
		qsort($map[$name], 'isMoreSpecific');
	}

	function &getFunctionFor(&$call) {
		$name = $call->getName();
		if ($this->map[$name] == null) {
			return false;
		}

		foreach ($this->map[$name] as $f) {
			if ($f->matches($call)) {
				return $f;
			}
		}

		return false;
	}
}

class ContextMDDefinitionsMap extends MDDefinitionsMap {
	function _addDefinition(&$map, $name, $f) {
		$map[$name][] =& $f;

		// Improve: insert in order, do not sort
		qsort($map[$name], 'isMoreSpecific');
	}

	function &getFunctionFor(&$call) {
		$name = $call->getName();
		if ($this->map[$name] == null) {
			return false;
		}

		foreach ($this->map[$name] as $f) {
			if ($f->matches($call)) {
				return $f;
			}
		}

		return false;
	}
}

class MDCompiler {
	var $map;

	function MDCompiler() {
		$this->LoadMap();
	}

	function loadMDFunctions($file) {
		$functions = Spyc::YAMLLoad($file);
		$defs = '';
		$md_map = $this->newDefinitionsMap();

		if (empty($functions)) {
			print_backtrace('There are no definitions in ' . $file);
		}
		else {
			foreach($functions as $f => $rules) {
				foreach ($rules as $rule) {
					$def = $this->defMDFunction($f, $rule);
					//echo 'Prueba: '. print_r($def,true) .'<br/>Rule: ' . print_r($rule,true) . '<br/>';
					if ($def != false) {
						$md_map->addDefinition($def->getName(), $def);
						$defs .= $def->define() . "\n\n";
					}
				}
			}
		}

		$defs .= 'define(\'' . $this->MapDefName() .'\', \'' . str_replace('\'', '\\\'', serialize($md_map)) . '\');';
		//print_r($defs);
		//echo $md_map->printHtml();

		eval($defs);

		return $defs;
	}

	function LoadMap() {
		eval('$this->map = unserialize('. $this->mapDefName(). ');');
	}
}

class OrdinaryMDCompiler extends MDCompiler {
	function &newDefinitionsMap() {
		return new OrdinaryMDDefinitionsMap();
	}

	function &defMDFunction($f, $rule_def) {
		return OrdinaryMDFunctionDef::LoadFromArray($f, $rule_def);
	}

	function MapDefName() {
		return 'ordinary_md_map';
	}

	function call($fname, $params) {
		$call =& new OrdinaryMDCall($fname, $params);

		$defs = $this->map->at($call->getName());
		foreach ($defs as $def) {
			if ($def->matches($call)) {
				return $def->execute($call);
			}
		}
	}
}

class ContextMDCompiler extends MDCompiler {
	function &newDefinitionsMap() {
		return new ContextMDDefinitionsMap();
	}

	function &defMDFunction($f, $rule_def) {
		return ContextMDFunctionDef::LoadFromArray($f, $rule_def);
	}

	function MapDefName() {
		return 'ctx_md_map';
	}

	function call($fname, &$component, $params) {
		$call =& new ContextMDCall($fname, $component, $params);

		$defs = $this->map->at($call->getName());

		//echo 'Seeking match for: ' . $fname . '<br/>';

		foreach ($defs as $def) {
			//echo 'Trying to match with: ' . print_r($def,true) . '<br/>';
			if ($def->matches($call)) {
				//echo 'Matched: ' . print_r($def,true) . '<br/>';
				return $def->execute($call);
			}
		}

		print_backtrace('MD failed');
		return null;
	}
}


class MDFunctionDef {

	function LoadFromArray($name, $ruledef) {
		print_backtrace(getClass($this) . ': Subclass responsability');
	}

	function define() {
		print_backtrace(getClass($this) . ': Subclass responsability');
	}

	function matches(&$call) {
		print_backtrace(getClass($this) . ': Subclass responsability');
	}

	function execute(&$call) {
		print_backtrace(getClass($this) . ': Subclass responsability');
	}

	function isMoreSpecificThan($g) {
		print_backtrace(getClass($this) . ': Subclass responsability');
	}
}

class OrdinaryMDFunctionDef extends MDFunctionDef {
	var $name;
	var $params;
	var $body;

	function LoadFromArray($name, $ruledef) {
		if (is_array($ruledef['in'])) {
			return false;
		}

		$f =& new OrdinaryMDFunctionDef;
		if (is_array($ruledef['with'])) {
			foreach($ruledef['with'] as $pname => $type) {
				$f->addParam($pname, $type);
			}
		}

		$f->name = $name;
		$f->body = $ruledef['do'];

		return $f;
	}

	function addParam($name, $type) {
		$this->params[] =& new OrdinaryParam($name, $type);
	}

	function getName() {
		return $this->name;
	}

	function getParams() {
		return $this->params;
	}

	function define() {
		$s = 'function ' . $this->definitionName();
		$pdefs = array();
		foreach($this->params as $param) {
			$pdefs[] = $param->getName();
		}
		$s .= '(' . implode(',', $pdefs) . ') {' . $this->body . '}';
		return $s;
	}

	function definitionName() {
		$s = $this->name;
		foreach($this->params as $param) {
			$s .= '_' . strtoupper($param->getType());
		}
		return $s;
	}

	function matches(&$call) {
		$res = true;

		$i = 0;

		while ($res and $i < count($this->params))  {
			$res = 	$this->params[$i]->matches($call->params[$i]);
			$i++;
		}

		return $res;
	}

	function &execute(&$call) {
		$args =& $call->getParams();
		for($i = 0; $i < count($args); $i++) {
			$params[] = '$args['.$i .']';
		}
		$res = null;
		eval('$res =& ' . $this->definitionName() . '('. implode(',', $params) .');');
		return $res;
	}

	function isMoreSpecificThan($g) {
		$gparams = $g->getParams();

		if (count($this->params) != count($gparams)) {
			print_backtrace('Cardinality error: ' . count($this->params) . ' <> ' . count($gparams));
			exit;
		}

		$res = false;

		$i = 0;

		while (!$res and $i < count($gparams)) {
			$res = $this->params[$i]->isMoreSpecificThan($gparams[$i]);
			$i++;
		}

		return $res;
	}

}

class ContextMDFunctionDef extends MDFunctionDef {
	var $name;
	var $params = array();
	var $body;
	var $context;

	function LoadFromArray($name, $ruledef) {
		if (!is_array($ruledef['in'])) {
			return false;
		}

		$f =& new ContextMDFunctionDef;

		$f->context =& new ContextParam($ruledef['in']);

		if (is_array($ruledef['with'])) {
			foreach($ruledef['with'] as $pname => $type) {
				$f->addParam($pname, $type);
			}
		}

		$f->body = $ruledef['do'];
		$f->name = $name;

		return $f;
	}

	function addParam($name, $type) {
		$this->params[] =& new OrdinaryParam($name, $type);
	}

	function &getContext() {
		return $this->context;
	}

	function &getParams() {
		return $this->params;
	}

	function getName() {
		return $this->name;
	}

	function define() {
		$s = 'function ' . $this->defName();
		$pdefs = array();
		foreach($this->params as $param) {
			$pdefs[] = $param->getName();
		}
		$ps = array_merge(array('&$_context'), $pdefs);
		$s .= '(' . implode(',', $ps) . ') {' . $this->body . '}';
		return $s;
	}

	function defName() {
		$s = $this->name;
		$s .= '_begctx';
		foreach($this->context->layers as $c) {
			$s .= '_' . strtoupper($c);
		}
		$s .= '_endctx';

		foreach($this->params as $param) {
			$s .= '_' . strtoupper($param->getType());
		}

		return $s;
	}

	function execute(&$call) {
		$args =& $call->getParams();
		for($i = 0; $i < count($args); $i++) {
			$params[] = '$args['.$i .']';
		}
		$res = null;
		eval('$res =& ' . $this->defName() . '('. implode(',', $params) .');');
		return $res;
	}

	function isMoreSpecificThan($g) {
		$gcontext =& $g->getContext();
		$mycontext =& $this->getContext();
		if ($gcontext == $mycontext) {
			$gparams = $g->getParams();

			$res = false;

			$i = 0;
			while (!$res and $i < count($gparams)) {
				$res = $this->params[$i]->isMoreSpecificThan($gparams[$i]);
				$i++;
			}

			return $res;
		}
		else {
			return $mycontext->isMoreSpecificThan($gcontext);
		}
	}

	function matches(&$call) {
		$mycontext =& $this->getContext();

		if (!$mycontext->matches($call->getContext())) {
			//echo 'Def: ' . print_r($this,true) . '<br/>';
			//echo 'Call: '. print_r($call->context->layers,true) . '<br/>';
			return false;
		}

		$res = true;

		$i = 0;

		while ($res and $i < count($this->params))  {
			$res = 	$this->params[$i]->matches($call->params[$i]);
			$i++;
		}

		return $res;
	}
}

class Param {
	function matches(&$callparam) {
		print_backtrace(getClass($this) . ': Subclass responsability');
	}

	function isMoreSpecificThan(&$param) {
		print_backtrace(getClass($this) . ': Subclass responsability');
	}
}

class OrdinaryParam extends Param {
	var $name;
	var $type;

	function OrdinaryParam($name, $type) {
		$this->name = $name;
		$this->type = $type;

	}

	function matches(&$callparam) {
		return is_a($callparam, $this->getType());
	}

	function isMoreSpecificThan(&$param) {
		return (($this->getType() != $param->getType()) and (str_is_a($this->getType(), $param->getType())));
	}

	function getType() {
		return $this->type;
	}

	function getName() {
		return $this->name;
	}
}

class ContextParam extends Param {
	var $layers;

	function ContextParam($layers) {
		$this->layers = $layers;
	}

	function getLayers() {
		return $this->layers;
	}

	function matches(&$callingContext) {
		$layers = $callingContext->getLayers();
		$mylayers = $this->getLayers();

		//echo 'Calling layers: ' . print_r($layers,true) . '<br/>';

		$n = count($mylayers);

		while (count($layers) > $n) {
			array_shift($layers);
		}

		//echo 'Reduced calling layers: ' . print_r($layers,true) . '<br/>';
		//echo 'Def layers: '. print_r($mylayers,true) . '<br/>';
		$res = true;
		$i = 0;

		while ($res and $i < count($layers)) {
		 	$layer = $layers[$i];
		 	//echo 'Comparing: '. $layers[$i] . ' and ' . $mylayers[$i] . '<br/>';
		 	$res = str_is_a($layers[$i], $mylayers[$i]);
			//echo 'Result: ' . $res . '<br/>';
		 	$i++;
		}


		return $res;
	}

	function isMoreSpecificThan(&$param) {
		$layers = $param->getLayers();
		$mylayers = $this->getLayers();
		$lc1 = count($layers);
		$lc2 = count($mylayers);

		//echo 'Comparing contexts:<br/>';
		//echo $lc1 . '<br/>';
		//echo $lc2 . '<br/>';

		if ($lc1 < $lc2) {
			return true;
		}
		else if ($lc1 > $lc2) {
			return false;
		}
		else {
			$res = false;
			$i = 0;

			while (!$res and $i < count($mylayers)) {
			 	$layer = $mylayers[$i];
			 	//echo 'Comparing layers:<br/>';
				//echo $layers[$i] . '<br/>';
				//echo $mylayers[$i] . '<br/>';
			 	$res = (($mylayers[$i] != $layers[$i]) and (str_is_a($mylayers[$i], $layers[$i])));
				//echo 'Result: ' . $res . '<br/>';

			 	$i++;
			}

			return $res;
		}
	}
}

function isMoreSpecific($f, $g) {
	return $f->isMoreSpecificThan($g);
}


class MDCall {
	function execute() {
		print_backtrace(getClass($this) . ': Subclass responsability');
	}
}

class OrdinaryMDCall extends MDCall {
	var $name;
	var $params;

	function OrdinaryMDCall($name, $params) {
		$this->name = $name;
		$this->params = $params;
	}

	function &getParams() {
		return $this->params;
	}

	function &getName() {
		return $this->name;
	}
}

class ContextMDCall extends MDCall {
	var $context;
	var $name;
	var $params;

	function ContextMDCall($name, &$component, $params) {
		$this->name =$name;
		$this->context =& new CallingContext($component);
		$this->params = $params;
	}

	function &getContext() {
		return $this->context;
	}

	function &getParams() {
		return $this->params;
	}

	function &getName() {
		return $this->name;
	}
}

class CallingContext {
	var $component;
	var $layers;

	function CallingContext(&$component) {
		$this->component =& $component;
		$this->calculateLayers();
	}

	function &getComponent() {
		return $this->component;
	}

	function &getLayers() {
		return $this->layers;
	}

	function calculateLayers() {
		$layers = array(strtoupper(get_class($this->component)));
		$c =& $this->component->getParent();

		$i = 0;
		while(($c !== null) and (getClass($c) != 'stdclass')) {
			array_push($layers, strtoupper(get_class($c)));

			$c =& $c->getParent();
			$i++;
		}
		$this->layers = array_reverse($layers);
	}
}


?>

