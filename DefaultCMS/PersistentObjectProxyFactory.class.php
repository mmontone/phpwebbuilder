<?php
class PersistentObjectProxyFactory {
	var $proxy;

	function PersistentObjectProxyFactory() {}

	function buildNew(& $target) {
		$this->proxy = & new PersistentObjectProxy();
		foreach (array_keys($target->fields) as $i) {
			$target->fields[$i]->visit($this);
		}
		return $this->proxy;
	}

	function & visitTextField(& $text_field) {
		return new ValueHolderProxy($text_field);
	}

	function & visitIndexField(& $index_field) {
		return new ValueHolderProxy($index_field);
	}
}

class Proxy {
	var $target;
	var $commands;

	function Proxy(& $target) {
		$this->target = & $target;
		$this->commands = array ();
	}

	function registerCommand($selector, & $value) {
		$this->commands[] = & new Command($this->target, $selector, $value);
	}

	function execute() {
		foreach (array_keys($this->commands) as $i) {
			$this->commands[$i]->execute();
		}
	}
}

class Command {
	var $selector;
	var $value;
	var $target;

	function Command(& $target, $selector, & $value) {
		$this->target = & $target;
		$this->selector = $selector;
		$this->value = & $value;
	}

	function execute() {
		$selector = $this->selector;
		$this->target->$selector ($this->value);
	}
}

class ValueHolderProxy extends Proxy {
	var $value;

	function ValueHolderProxy(& $target) {
		$this->target = & $target;
		$this->value = & $target->getValue();
	}

	function setValue(& $value) {
		$this->registerCommand('setValue', $value);
		$this->value = & $value;
	}

	function & getValue() {
		return $this->value;
	}
}

class UnitOfWork {
	var $commands;

	function registerCommand($selector, & $value) {
		$this->commands[] = & new Command($this->target, $selector, $value);
	}

	function commit() {
		foreach (array_keys($this->commands) as $i) {
			$this->commands[$i]->execute();
		}
	}

	function undo() {
		$this->timesUndo(1);
	}

	function timesUndo($n) {
		foreach (array_keys($this->commands) as $i) {
			$this->commands[$i]->undo();
		}
	}

	function undoFrom($command_pos) {
		$last_pos = key(end($this->commands));
		for ($pos = $command_pos; $pos < $last_pos; $pos++) {
			$this->commands[$pos]->undo();
			unset ($this->commands[$pos]->undo());
		}

	}
}
?>