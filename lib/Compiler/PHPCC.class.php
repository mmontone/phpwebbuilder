<?php

require_once 'Parser.class.php';

class PHPCC {

    function &createGrammar($grammar) {
    	$g =& new Grammar(array(
    		'root'=>'grammar',
    		'nt'=>array(
    			'alternative'=>new ListParser(new SeqParser(array(new MaybeParser(
    							new SeqParser(array(new Identifier, new Symbol('=>')))),new SubParser('sequence'))),new Symbol('\|')),
    			'maybe'=>new SeqParser(array(new Symbol('\['),new SubParser('alternative'),new Symbol('\]'))),
    			'list'=>new SeqParser(array(new Symbol('\{'),new SubParser('alternative'),new Symbol(';'),new SubParser('alternative'),new Symbol('\}'))),
    			'sequence'=>new ListParser(new SeqParser(array(new MaybeParser(
    							new SeqParser(array(new Identifier, new Symbol('->')))),
    							new AltParser(array(
    								new SubParser('list'),
    								new SubParser('maybe'),
    								new SubParser('symbol'),
    								new SubParser('subparser'),
    								'alt'=>new SeqParser(array(new Symbol('\('),new SubParser('alternative'),new Symbol('\)'))),
    							)))),new Symbol(',')),
    			'subparser'=>new SeqParser(array(new Symbol('<'),'name'=>new Identifier,new Symbol('>'),)),
    			'symbol'=>new Symbol('"[^"]+"'),
    			'non-terminal'=>new SeqParser(array(new Identifier, new Symbol('::='), new SubParser('alternative'), new Symbol('\.'))),
    			'grammar'=>new SeqParser(array(new Symbol('\<'),new Identifier, new Symbol('\('),new MultiParser(new SubParser('non-terminal')), new Symbol('\)'),new Symbol('\>')))
    		)));
    	$g->addPointCuts(array(
    			'alternative'=>new FObject($n=null, 'PHPCC::createAlternative'),
    			'maybe'=>new FObject($n=null, 'PHPCC::createMaybe'),
    			'list'=>new FObject($n=null, 'PHPCC::createList'),
    			'sequence'=>new FObject($n=null, 'PHPCC::createSequence'),
    			'grammar'=>new FObject($n=null, 'PHPCC::createNTS'),
				'symbol'=>new FObject($n=null, 'PHPCC::createSymbol'),
				'subparser'=>new FObject($n=null, 'PHPCC::createSubparser'),
		));
		return $g->compile($grammar);
    }
    function &createSequence(&$params){
	    $ks = array_keys($params);
	    for($i=0;$i<count($params);$i+=2){
	    	$param = $params[$ks[$i]];
	    	if ($param[1]['selector']==='alt'){
	    		$param[1]['result']=&$param[1]['result'][1];
	    	}
	    	if ($param[0]===null) {
	    		$ret [] = $param[1]['result'];
	    	} else {
	    		$ret [$param[0][0]] = $param[1]['result'];
	    	}
	    }
	    $seq =& new SeqParser($ret);
	    return $seq;
    }
    function &createAlternative(&$params){
	    $ks = array_keys($params);
	    for($i=0;$i<count($params);$i+=2){
	    	$param = $params[$ks[$i]];
	    	if ($param[0]===null) {
	    		$ret [] = $param[1];
	    	} else {
	    		$ret [$param[0][0]] = $param[1];
	    	}
	    }
	    $alt =& new AltParser($ret);
	    return $alt;
    }
    function &createNTS(&$params){
    	foreach($params[3] as $param) {
    		$ret [$param[0]]=$param[2];
    	}
    	$g =& new Grammar(array('root'=>$params[1],'nt'=>&$ret));
    	return $g;
    }
    function &createList(&$elems){
    	$lp =& new ListParser($elems[1],$elems[3]);
    	return $lp;
    }
    function &createSymbol(&$sym){
    	$sym = substr(substr($sym, 1),0, -1);
    	$s =& new Symbol($sym);
    	return $s;
    }
    function &createSubparser(&$sp){
    	$s =& new SubParser($sp['name']);
    	return $s;
    }
    function &createMaybe(&$sp){
    	$s =& new MaybeParser($sp[1]);
    	return $s;
    }
}

?>