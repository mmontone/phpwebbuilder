<?php

require_once 'spyc-0.2.3/spyc.php';
require_once 'qsort.php';

function defmdfsignature($text) {
    preg_match('/([[:alpha:]]*)[\s\t]*(\[\])?[\s\t]*\((.*)\)/s', $text, $matches);
    //print_r($matches);
    $name = $matches[1];
    $with_context = $matches[2];
    $params = $matches[3];

    //echo 'Name: ' . $name;
    //echo 'Params: ' . $params;

    $rules = array();

    $ps = explode(',', $params);
    $pss = array();
    foreach($ps as $p) {
        $pp = explode(':', $p);
        $arg = trim($pp[0]);
        $type = trim($pp[1]);
        $pss[$arg] = $type;
    }
    $pss;

    $compiler =& MDCompiler::Instance();
    $def =& $compiler->defMDFunctionSignature($name, $pss, $with_context != '');
    return $def->define();
}

function defmdf($text) {
    preg_match('/([[:alpha:]]*)[\s\t]*(?:\[(.*)\])?[\s\t]*\((.*)\)[\s\t]*\{(.*)\}/s', $text, $matches);
    //print_r($matches);
    $name = $matches[1];
    $context = $matches[2];
    $params = $matches[3];
    $body = $matches[4];
    //echo 'Name: ' . $name;
    //echo 'Context: ' . $context;
    //echo 'Params: ' . $params;
    //echo 'Body:' . $body;

    $rules = array();
    if ($context != '') {
        $cs = explode('<-',$context);
        foreach (array_keys($cs) as $i) {
            $cs[$i] = trim($cs[$i]);
        }
        $rules['in'] = $cs;
    }

    $ps = explode(',', $params);
    $pss = array();
    foreach($ps as $p) {
        $pp = explode(':', $p);
        $arg = trim($pp[0]);
        $type = trim($pp[1]);
        $pss[$arg] = $type;
    }
    $rules['with'] = $pss;
    $rules['do'] = $body;
    //print_r($rules);
    //echo '<br /><br />';
    $compiler =& MDCompiler::Instance();
    $def =& $compiler->defMDFunction($name, $rules);
    return $def->define();
}

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
    var $signatures = array();
    var $def_num = 1;

	function addDefinition(&$def) {
		if ($this->map[$def->getName()] == null) {
			echo 'Warning: Signature not available for ' . $def->getName() . '<br/>';
            $this->addSignature($def->getSignature());
		}

		$def->setDefinitionName($this->def_num++);
        $this->map[$def->getName()]->addDefinition($def);
	}

    function addSignature(&$sig) {
        if (isset($this->map[$sig->getName()])) {
        	echo 'Warning: redifining signature. Definitions removed<br/>';
        }
        $slot =& new MDDefinitionSlot;
        $slot->signature =& $sig;
        $this->map[$sig->getName()] =& $slot;
    }

	function &getDefinitionFor(&$call) {
        $name = $call->getName();
        if ($this->map[$name] == null) {
            return false;
        }

        return $this->map[$name]->getDefinitionFor($call);
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

class MDDefinitionSlot {
	var $signature;
    var $defs=array();
    var $sorted_defs = array();
    var $sorted=false;

    function addDefinition(&$def) {
        if (isset($this->defs[$def->hash()])) {
        	echo 'Warning: redefining ' . $def->getSignature()->printString() . '<br/>';
        }

        $this->defs[$def->hash()] =& $def;
        $this->sorted=false;
    }

    function getDefinitionFor(&$call) {
        if (!$this->sorted) {
            $this->sorted_defs = arrat_values($this->defs);
            qsort($this->sorted_defs, 'isMoreSpecific');
            $this->sorted=true;
        }

        foreach (array_keys($this->sorted_defs) as $d) {
            $def =& $this->sorted_defs[$d];
            if ($d->matches($call)) {
                return $d;
            }
        }

        return false;
    }
}

class OrdinaryMDDefinitionsMap extends MDDefinitionsMap {
	function &getDefinitionFor(&$call) {
		$name = $call->getName();
		if ($this->map[$name] == null) {
			return false;
		}

		return $this->map[$name]->getDefinitionFor($call);
	}
}

class ContextMDDefinitionsMap extends MDDefinitionsMap {
	function _addDefinition(&$map, &$def) {
		$map[$def->getName()]->addDefinition($def);
	}

	function &getDefinitionFor(&$call) {
		$name = $call->getName();
		if ($this->map[$name] == null) {
			return false;
		}
        return $this->map[$name]->getDefinitionFor($call);
	}
}

class MDCompiler {
	var $ordinaries_map;
    var $context_map;
    var $function_diff = 1;

	function MDCompiler() {
        $this->ordinaries_map =& new MDDefinitionsMap;
        $this->context_map =& new MDDefinitionsMap;
	}

    function &Instance() {
    	return Session::getAttributeOrSet('mdcompiler', new MDCompiler);
    }

    function loadMDDefinitionsFromFile($file) {
		$definitions = Spyc::YAMLLoad($file);

		if (empty($definitions)) {
			print_backtrace('There are no definitions in ' . $file);
		}
		else {
		    return $this->loadMDDefinitions($definitions);
		}
	}

    /*
    function loadMDDefinitions($definitions) {
	    $defs = '';
        $md_map =& $this->getDefinitionsMap();
        foreach($definitions as $f => $rules) {
            foreach ($rules as $rule) {
                $def =& $this->defMDFunction($f, $rule);
                //echo 'Prueba: '. print_r($def,true) .'<br/>Rule: ' . print_r($rule,true) . '<br/>';
                if ($def != false) {
                    $md_map->addDefinition($def);
                    $defs .= $def->define($this->function_diff++) . "\n\n";
                }
            }
        }

        $defs .= 'define(\'' . getClass($this) .'\', \'' . str_replace('\'', '\\\'', serialize($md_map)) . '\');';

        print_r($defs);
        echo $md_map->printHtml();

        eval($defs);

        return $defs;
    }*/

    function &defMDFunction($f, $rule_def) {
        if (!empty($rule_def['in'])) {
            $def =& ContextMDFunctionDef::LoadFromArray($f, $rule_def);
            $this->context_map->addDefinition($def);
            return $def;
        }
        else {
            $def =& OrdinaryMDFunctionDef::LoadFromArray($f, $rule_def);
            $this->ordinaries_map->addDefinition($def);
            echo 'Defining ' . $def->define() . '<br/>';
            return $def;
        }
    }

    function defMDFunctionSignature($name, $params, $isOrdinary=true) {
        $sig =& new MDFunctionSignature($name, $params);

        if ($isOrdinary) {
            $this->ordinaries_map->addSignature($sig);
        }
        else {
            $this->context_map->addSignature($sig);
        }
    }

    function call($fname, $params) {
        $call =& new OrdinaryMDCall($fname, $params);

        $defs = $this->map->at($call->getName());

        foreach (array_keys($defs) as $d) {
            if ($defs[$d]->matches($call)) {
                return $defs[$d]->execute($call);
            }
        }
    }

    function callInContext($fname, &$context, $params) {
        $call =& new ContextMDCall($fname, $context, $params);

        $defs = $this->map->at($call->getName());

        //echo 'Seeking match for: ' . $fname . '<br/>';

        foreach (array_keys($defs) as $d) {
            // echo 'Trying to match with: ' . print_r($def,true) . '<br/>';
            if ($defs[$d]->matches($call)) {
                //echo 'Matched: ' . print_r($def,true) . '<br/>';
                return $defs[$d]->execute($call);
            }
        }

        print_backtrace('MD failed');
        return null;
    }
}

/*
class OrdinaryMDCompiler extends MDCompiler {

}

class ContextMDCompiler extends MDCompiler {
	function &getDefinitionsMap() {
		$s =& Session::Instance();
        return $s->getAttribute('contextmdmap');
	}

	function &defMDFunction($f, $rule_def) {
		return ContextMDFunctionDef::LoadFromArray($f, $rule_def);
	}

	function call($fname, &$component, $params) {
		$call =& new ContextMDCall($fname, $component, $params);

		$defs = $this->map->at($call->getName());

		//echo 'Seeking match for: ' . $fname . '<br/>';

		foreach (array_keys($defs) as $d) {
            // echo 'Trying to match with: ' . print_r($def,true) . '<br/>';
			if ($defs[$d]->matches($call)) {
				//echo 'Matched: ' . print_r($def,true) . '<br/>';
				return $defs[$d]->execute($call);
			}
		}

		print_backtrace('MD failed');
		return null;
	}
}
*/

class MDFunctionSignature {
    var $name;
    var $params;


    function MDFunctionSignature($name, $params) {
    	$this->name = $name;
        $this->params = $params;
    }

    function getParams() {
        return $this->params;
    }

    function matches(&$def) {
        return ($def->getName() == $this->getName()) and
               (count($def->getParams()) == count($this->getParams()));
    }

    function printString() {
        $params = array();
        foreach (array_keys($this->params) as $p) {
            $params[] = $this->params[$p]->printString();
        }

        return $this->getName() . '(' . implode(',', $params) . ')';
    }

    function getName() {
    	return $this->name;
    }

    function define() {
    	return '';
    }
}

class MDFunctionDef {
    var $definition_name;

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

    function &getSignature() {
        return new MDFunctionSignature($this->name, $this->params);
    }

    function getDefinitionName() {
        return $this->definition_name;
        /*
        $s = $this->name;
        foreach($this->params as $param) {
            $s .= '_' . strtoupper($param->getType());
        }
        return $s;*/
    }

    function setDefinitionName($diff) {
        $this->definition_name = $this->getName() . '_' . $diff;
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

    function hash() {
    	$hash = $this->getName();
        foreach(array_keys($this->params) as $p) {
        	$hash .= $this->params[$p]->getType();
        }

        return $hash;
    }

	function addParam($name, $type) {
		if ($type == null) {
			$this->params[] =& new UntypedParam($name);
		}
        else {
            $this->params[] =& new OrdinaryParam($name, $type);
        }
	}

	function getName() {
		return $this->name;
	}

	function getParams() {
		return $this->params;
	}

	function define() {
		$s = 'function ' . $this->getDefinitionName();
        $pdefs = array();
        foreach(array_keys($this->params) as $p) {
            $pdefs[] = $this->params[$p]->getName();
        }
        $s .= '(' . implode(',', $pdefs) . ') {' . $this->body . '}';
        return $s;
    }

    function printString() {
    	$s = 'function ' . $this->hash();
        $pdefs = array();
        foreach(array_keys($this->params) as $p) {
            $pdefs[] = $this->params[$p]->getName() . ':' . $this->params[$p]->getType();
        }
        $s .= '(' . implode(',', $pdefs) . ') {' . $this->body . '}';
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
		eval('$res =& ' . $this->getDefinitionName() . '('. implode(',', $params) .');');
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
    var $definition_name;

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

    function hash() {
        $hash = $this->getName();

        foreach(array_keys($this->params) as $p) {
            $hash .= $this->params[$p]->getType();
        }

        foreach(array_keys($this->params) as $p) {
            $hash .= $this->params[$p]->getType();
        }

        return $hash;
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
		$s = 'function ' . $this->getDefinitionName();
		$pdefs = array();
		foreach(array_keys($this->params) as $p) {
            $pdefs[] = $this->params[$p]->getName();
		}
		$ps = array_merge(array('&$_context'), $pdefs);
		$s .= '(' . implode(',', $ps) . ') {' . $this->body . '}';
		return $s;
	}

    function printString() {

    }

	function getDefinitionName() {
		return $this->definition_name;
        /*
        $s = $this->name;
		$s .= '_begctx';
		foreach($this->context->layers as $c) {
			$s .= '_' . strtoupper($c);
		}
		$s .= '_endctx';

		foreach($this->params as $param) {
			$s .= '_' . strtoupper($param->getType());
		}

		return $s;*/
	}

	function execute(&$call) {
		$args =& $call->getParams();
		for($i = 0; $i < count($args); $i++) {
			$params[] = '$args['.$i .']';
		}
		$res = null;
		eval('$res =& ' . $this->getDefinitionName() . '('. implode(',', $params) .');');
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

    function printString() {
    	return $this->getName() . ' : ' . $this->getType();
    }
}

class UntypedParam extends Param {
    var $name;

    function UntypedParam($name) {
        $this->name = $name;
        parent::Param();
    }

    function matches(&$callparam) {
        return true;
    }

    function isMoreSpecificThan(&$param) {
        return true;
    }

    function getName() {
        return $this->name;
    }

    function printString() {
        return $this->getName();
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

function isMoreSpecific(&$f, &$g) {
	echo 'Comparing specifiness of ' . getClass($f) . ' against ' . getClass($g) . '<br/>';
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