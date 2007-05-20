<?php
class DbgWindow extends Component {
	var $root;

	function DbgWindow(& $root) {
		$this->root = & $root;
		parent :: Component();
	}

	function initialize() {
		$this->addComponent($this->root, 'root');
		$logger = & new DbgLogger;
		$this->addComponent($logger, 'logger');
		$this->addComponent(new DbgInfo, 'info');
		$logger->addInterestIn('object_selected', new FunctionObject($this, 'inspectObject'));
		$app_menu = & new AppMenu;
		$app_menu->registerCallback('inspect_application', new FunctionObject($this, 'inspectApplication'));
		$this->addComponent($app_menu, 'app_menu');
	}

	function inspectApplication() {
		$this->addInspector($this->root);
	}

	function inspectObject(& $logger, & $object) {
		$this->addInspector($object);
	}

	function addInspector(& $object) {
		$inspector = & $this->getInspectorFor($object);
		$inspector_navigator = & new InspectorNavigator($inspector);
		$this->addComponent($inspector_navigator, $object->getInstanceId() . '_inspector');
	}

	function & getInspectorFor(& $object) {
		if (is_object($object) and !is_a($object, 'PWBObject')) {
			return new PrimitiveObjectInspector($object);
		}

		if (is_null($object)) {
			return new NullInspector;
		}

		return mdcall('getInspectorFor', array (
			& $object
		));
	}
}


class AppMenu extends Component {
	function initialize() {
		$this->addComponent(new CommandLink(array (
			'text' => 'Inspect application',
			'proceedFunction' => new FunctionObject($this,
			'inspectApplication'
		))), 'inspect_app');
		$this->addComponent(new Link(constant('site_url') . '?restart=yes', 'Restart'), 'restart');
		$this->addComponent(new Link(constant('site_url') . '?recompile=yes', 'Recompile'), 'recompile');
		$this->addComponent(new Link(constant('site_url') . '?restart=yes&recompile=yes', 'Restart and recompile'), 'restart_and_recompile');
	}

	function inspectApplication() {
		$this->callback('inspect_application');
	}
}

#@defmdf getInspectorFor(&$o : PWBObject)
{
	return new PWBObjectInspector($o);
} //@#

#@defmdf getInspectorFor(&$o : String)
{
	return new StringInspector($o);
} //@#

#@defmdf getInspectorFor(&$o : Array)
{
	return new ArrayInspector($o);
} //@#

#@defmdf getInspectorFor(&$o : Integer)
{
	return new IntegerInspector($o);
} //@#

#@defmdf getInspectorFor(&$o : Boolean)
{
	return new BoolInspector($o);
} //@#

#@defmdf getInspectorFor(&$o : Component)
{
	return new ComponentInspector($o);
} //@#

class InspectorNavigator extends Component {
	var $inspector;

	function InspectorNavigator(& $inspector) {
		$this->inspector = & $inspector;
		parent :: Component();
	}

	function initialize() {
		$this->addComponent($this->inspector, 'body');
		$this->addComponent(new CommandLink(array (
			'text' => 'Back',
			'proceedFunction' => new FunctionObject($this,
			'back'
		))), 'back');
		$this->addComponent(new CommandLink(array (
			'text' => 'Close',
			'proceedFunction' => new FunctionObject($this,
			'close'
		))), 'close');
	}

	function close() {
		$this->delete();
	}

	function back() {
		$this->body->callback();
	}
}

class Inspector extends Component {
	var $object;

	function Inspector(& $object) {
		$this->object = & $object;
		parent :: Component();
	}

	function initialize() {
		$this->addComponent(new Label($this->getTitle()), 'title');
		$this->addComponent(new Label($this->getType()), 'type');
	}

	function getTitle() {
		return print_object($this->object);
	}

	function getType() {
		if (is_object($this->object)) {
			return get_class($this->object);
		} else {
			return gettype($this->object);
		}
	}

	function addInspectLink(& $object, $slot) {
		$this->addComponent(new CommandLink(array (
			'text' => print_object($object
		), 'proceedFunction' => new FunctionObject($this, 'inspectObject', array (
			'object' => & $object
		)))), $slot);
	}

	function inspectObject($params) {
		$object = & $params['object'];
		$inspector = & $this->getInspectorFor($object);
		$this->call($inspector);
	}

	function & getInspectorFor(& $object) {
		if (is_object($object) and !is_a($object, 'PWBObject')) {
			return new PrimitiveObjectInspector($object);
		}

		if (is_null($object)) {
			return new NullInspector;
		}

		return mdcall('getInspectorFor', array (
			& $object
		));
	}
}

class IntegerInspector extends Inspector {
}
class StringInspector extends Inspector {
}
class BoolInspector extends Inspector {
}
class NullInspector extends Inspector {}
// TODO: make a Navigation Inspector for Arrays
class ArrayInspector extends Inspector {}


class ObjectInspector extends Inspector {
	function initialize() {
		parent :: initialize();
		$discard_vars = $this->discardVars();
		foreach (array_keys(get_object_vars($this->object)) as $var) {
			if (!in_array($var, $discard_vars)) {
				$this->addVarEntry($var);
			}
		}
	}

	function addVarEntry($var) {
		$entry = & new VarEntry($this->object-> $var, $var);
		$this->addComponent($entry, 'var_' . $var);
		$entry->addInterestIn('inspect_object', new FunctionObject($this, 'inspectVar'));
	}

	function inspectVar(& $triggerer, & $var) {
		$params = array (
			'object' => & $var
		);
		$this->inspectObject($params);
	}

	function discardVars() {
		return array (
			'__instance_id'
		);
	}
}

class PrimitiveObjectInspector extends ObjectInspector {}

class PWBObjectInspector extends ObjectInspector {
	function initialize() {
		parent :: initialize();
		$this->addComponent(new Label($this->object->getInstanceId()), 'instance_id');
	}
}

class ComponentInspector extends PWBObjectInspector {
	function initialize() {
		parent :: initialize();
		$this->addInspectLink($this->object->owner, 'owner');
		foreach (array_keys($this->object->__children) as $c) {
			$this->addChildInspectLink($this->object->__children[$c]->getComponent(), $c);
		}
	}

	function addChildInspectLink(& $child, $slot) {
		$this->addComponent(new ChildEntry($child, $slot), $slot);
		$this-> $slot->addInterestIn('inspect_object', new FunctionObject($this, 'inspectChild'));
	}

	function inspectChild(& $triggerer, & $child) {
		$params = array (
			'object' => & $child
		);
		$this->inspectObject($params);
	}

	function discardVars() {
		return array (
			'__children',
			'owner',
			'__instance_id'
		);
	}
}

class VarEntry extends Component {
	var $child;
	var $slot;

	function VarEntry(& $child, $slot) {
		$this->child = & $child;
		$this->slot = $slot;
		parent :: Component();
	}

	function initialize() {
		$this->addComponent(new CommandLink(array (
			'text' => print_object($this->child
		), 'proceedFunction' => new FunctionObject($this, 'inspectObject'))), 'link');
		$this->addComponent(new Label($this->slot), 'slot');
	}

	function inspectObject() {
		$this->triggerEvent('inspect_object', $this->child);
	}
}

class ChildEntry extends VarEntry {
}

class DbgLogger extends Component {
	function initialize() {
		$this->addComponent(new LogList, 'list');
		$this->list->addInterestIn('object_selected', new FunctionObject($this, 'objectSelected'));
		$this->addComponent(new CommandLink(array (
			'text' => 'clear',
			'proceedFunction' => new FunctionObject($this,
			'clear'
		))), 'clear');
	}

	function addEntry(& $entry) {
		$this->list->addEntry($entry);
	}

	function clear() {
		$this->list->clear();
	}

	function objectSelected(& $list, & $object) {
		$this->triggerEvent('object_selected', $object);
	}
}

class LogList extends Component {
	function addEntry(& $entry) {
		$entry->addInterestIn('object_selected', new FunctionObject($this, 'objectSelected'));
		$this->addComponent($entry);
	}

	function clear() {
		$this->deleteChildren();
	}

	function objectSelected(& $entry, & $object) {
		$this->triggerEvent('object_selected', $object);
	}
}

function sql_log($array) {
	$entry = & new SQLLogEntry($array);
	$app = & Window :: getActiveInstance();
	$dbg_wnd = & $app->getComponent();
	if (is_a($dbg_wnd, 'DbgWindow')) {
		$dbg_wnd->logger->addEntry($entry);
	}
}

class LogEntry extends Component {
	var $array;
	var $backtrace;

	function LogEntry($array) {
		$this->array = $array;
		$this->backtrace = backtrace_string("\n");
		parent :: Component();
	}

	function initialize() {
		$components = & $this->getComponents();
		foreach (array_keys($components) as $c) {
			$component = & $components[$c];
			$this->addComponent($component);
		}
		$this->addComponent(new CommandLink(array('text' => '(bt)', 'proceedFunction' => new FunctionObject($this,'openCloseBacktrace'))));
	}

	function openCloseBacktrace() {
		if ($this->bt == null) {
			$this->addComponent(new Label($this->backtrace), 'bt');
		}
		else {
			$this->deleteComponentAt('bt');
		}
	}

	function getComponents() {
		$cs = array ();

		foreach (array_keys($this->array) as $i) {
			$elem = & $this->array[$i];
			if (is_string($elem)) {
				$cs[] = & new Label($elem);
			} else {
				$cs[] = & new CommandLink(array (
					'text' => print_object($elem
				), 'proceedFunction' => new FunctionObject($this, 'elementSelected', array (
					'element' => & $elem
				))));
			}
		}

		return $cs;
	}

	function elementSelected($params) {
		$element = & $params['element'];
		$this->triggerEvent('object_selected', $element);
	}

}

class SQLLogEntry extends LogEntry {

}

class EventTrackingLogEntry extends LogEntry {

}

class DbgInfo extends Component {
	function initialize() {
		//$constants = get_defined_constants();
		$constants = array (
			'modules',
			'page_renderer',
			'PHP5',
			'pwbdir',
			'site_url',
			'basedir',
			'compile'
		);
		foreach ($constants as $constant) {
			$this->addComponent(new DbgInfoConstant($constant), $constant);
		}
	}
}

class DbgInfoConstant extends Component {
	var $constant;

	function DbgInfoConstant($constant) {
		$this->constant = $constant;
		parent :: Component();
	}

	function initialize() {
		$this->addComponent(new Label($this->constant), 'const');
		$this->addComponent(new Label(constant($this->constant)), 'value');
	}
}
?>