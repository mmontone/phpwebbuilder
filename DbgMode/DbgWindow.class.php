<?php
class DbgWindow extends Component {
	var $root_obj;

	function DbgWindow(& $root) {
		$this->root_obj = & $root;
		parent :: Component();
	}

	function initialize() {
		$this->addComponent($this->root_obj, 'root');

        // The logger
        $logger = & new DbgLogger;

        if (constant('dbgmode') == 'wndmode') {
            $w =& new Window($logger, 'Logger');
            $w->open();
        }
        else {
            $this->addComponent($logger, 'logger');
        }

        $this->logger =& $logger;
        $logger->addInterestIn('object_selected', new FunctionObject($this, 'inspectObject'));

        // The debugging info

        $dbginfo =& new DbgInfo;
        /*if (constant('dbgmode') == 'wndmode') {
            $w =& new Window($dbginfo, 'Debugging info');
            $w->open();
        }
        else {*/
            $this->addComponent($dbginfo, 'info');
        //}

        // The application menu
		$app_menu = & new AppMenu;
		$app_menu->registerCallback('inspect_application', new FunctionObject($this, 'inspectApplication'));

        /*if (constant('dbgmode') == 'wndmode') {
            $w =& new Window($app_menu, 'Application menu');
            $w->open();
        }
        else {*/
           $this->addComponent($app_menu, 'app_menu');
        //}
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

		if (constant('dbgmode') == 'wndmode') {
            $w =& new Window($inspector_navigator, print_object($object) . ' inspector');
            $w->open();
        }
        else {
            $this->addComponent($inspector_navigator);
        }
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
class ArrayInspector extends Inspector {
    var $pageSize;
    var $pageNum;

    function initialize() {
        parent::initialize();
        $this->pageSize =& new ValueHolder(2);
        $this->pageNum =& new ValueHolder(1);
        $this->addWidgets();
    }

    function addWidgets() {
        //$this->deleteChildren();
        $this->addComponent(new Label(count($this->object)), 'size_label');
        $this->addComponent(new Input($this->pageSize), 'page_size');
        $this->addComponent(new Input($this->pageNum), 'page_num');
        $this->addComponent(new CommandLink(array('text' => '<<', 'proceedFunction' => new FunctionObject($this, 'previous'))), 'previous_btn');
        $this->addComponent(new CommandLink(array('text' => '>>', 'proceedFunction' => new FunctionObject($this, 'next'))), 'next_btn');
        $this->addComponent(new CommandLink(array('text' => 'refresh', 'proceedFunction' => new FunctionObject($this, 'showElements'))), 'refresh_btn');
        $this->showElements();
    }

    function showElements() {
        //$this->deleteComponentAt('elements');
        $this->addComponent(new Component, 'elements');
        $this_page = ($this->pageNum->getValue() - 1) * $this->pageSize->getValue();
        $i = $this_page;

        $keys = array_keys($this->object);

        while ($i < count($keys) and $i < ($this_page + $this->pageSize->getValue())) {
            $key = $keys[$i];
            $value =& $this->object[$key];
            $array_elem =& new ArrayElement($key, $value);
            $array_elem->addInterestIn('object_selected', new FunctionObject($this, 'objectSelected'));
            $this->elements->addComponent($array_elem);
            $i++;
        }
    }

    function objectSelected(&$triggerer, $params) {
        $this->inspectObject($params);
    }

    function next() {
        $this->pageNum->setValue($this->pageNum->getValue() + 1);
        $this->addWidgets();
    }

    function previous() {
        $this->pageNum->setValue($this->pageNum->getValue() - 1);
        $this->addWidgets();
    }

    function checkNextPermissions() {
        return ((($this->pageNum->getValue() - 1) * $this->pageSize->getValue())  + $this->pageSize->getValue()) < count($this->object);
    }

    function checkPreviousPermissions() {
        return $this->pageNum->getValue() > 1;
    }

    function refresh() {
        $this->deleteChildren();
        $this->addWidgets();
    }
}

class ArrayElement extends Component {
    var $k;
    var $v;

    function ArrayElement($key, &$value) {
        $this->k = $key;
        $this->v =& $value;
        parent::Component();
    }

    function initialize() {
        $this->addComponent(new CommandLink(array('text' => print_object($this->k), 'proceedFunction' => new FunctionObject($this, 'keySelected'))), 'key');
        $this->addComponent(new CommandLink(array('text' => print_object($this->v), 'proceedFunction' => new FunctionObject($this, 'valueSelected'))), 'value');
    }

    function keySelected() {
        $params = array('object' => $this->k);
        $this->triggerEvent('object_selected', $params);
    }

    function valueSelected() {
        $params = array('object' => &$this->v);
        $this->triggerEvent('object_selected', $params);
    }
}



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
		$entry = & new VarEntry($this->object->$var, $var);
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
		$this->$slot->addInterestIn('inspect_object', new FunctionObject($this, 'inspectChild'));
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
	var $_slot;

	function VarEntry(& $child, $slot) {
		$this->child = & $child;
		$this->_slot = $slot;
		parent :: Component();
	}

	function initialize() {
		$this->addComponent(new CommandLink(array (
			'text' => print_object($this->child
		), 'proceedFunction' => new FunctionObject($this, 'inspectObject'))), 'link');
		$this->addComponent(new Label($this->_slot), 'slot');
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
    dbg_log($entry);
}

function dbg_log(&$entry) {
	$app = & Window :: getActiveInstance();
    $dbg_wnd = & $app->getComponent();
    if (is_a($dbg_wnd, 'DbgWindow') and (is_object($dbg_wnd->logger))) {
        $dbg_wnd->logger->addEntry($entry);
    }
}

function event_log($array) {
    $entry = & new EventTrackingLogEntry($array);
    dbg_log($entry);
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

class SQLLogEntry extends LogEntry {}

class EventTrackingLogEntry extends LogEntry {}

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