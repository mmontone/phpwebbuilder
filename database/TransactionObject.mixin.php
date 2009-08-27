<?php

#@mixin TransactionObject
{
	var $modifications = array ();
	var $objects = array ();
	var $commands = array (); /* Undoable commands */
	function rollbackModifications(){
		$original_modifications = array_reverse($this->modifications);
		foreach (array_keys($original_modifications) as $key) {
			$mod = & $original_modifications[$key];
			/*if ($mod!=null){ Possible array accessing with & somewhere */
				$mod->rollback();
			/*}*/
		}
	}
	function cleanUp(){
		$a = array ();
		$this->modifications = & $a;
		$b = array ();
		$this->objects = & $b;
		$c = array ();
		$this->transaction_queries = & $c;
		$d = array ();
		$this->commands = & $d;
	}
	function registerAllModifications(&$trans){
		#@tm_echo echo $this->debugPrintString() . ' adding modifications from ' . $trans->debugPrintString() . '<br/>';@#
		$mods =& $trans->modifications;
		$count = count($mods);
		foreach(array_keys($mods) as $i){
			$this->modifications[] =& $mods[$i];
		}
		$comms =& $trans->commands;
		$count = count($comms);
		foreach(array_keys($comms) as $i){
			$this->commands[] =& $comms[$i];
		}
		$objs =& $trans->objects;
		$count = count($objs);
		foreach(array_keys($objs) as $i){
			$this->objects[] =& $objs[$i];
		}
		$trans->cleanUp();
		#@tm_echo echo $this->debugPrintString() . ' final state<br/>';@#
	}
	function runCommands(){
		foreach($this->commands as $com){
			$com->runCommand();
		}
	}
	function rebuild(){
		$objs =& $this->commands;
		$key = array_keys($objs);
		foreach($key as $i){
			$objs[$i]->rollback();
		}
	}
	function debugPrintString() {
		return print_object($this, ' modifications: ' . count($this->modifications) .' commands: ' . count($this->commands) . ' thread: ' . ($this->thread?$this->thread->debugPrintString():'No Thread') . ' metaclass: ' . print_object($this->metaclass). ' parent: '.($this->parent?$this->parent->debugPrintString():'No Parent'));
	}
}//@#
?>
