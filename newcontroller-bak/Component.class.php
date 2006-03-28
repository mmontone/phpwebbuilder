<?php

require_once dirname(__FILE__) . '/Action.class.php';
require_once dirname(__FILE__) . '/PWBObject.class.php';

class Component extends PWBObject
{
	var $listener;
    var $holder;
	var $registered_callbacks;
	var $configuration;
	var $__children;
	var $__actions;
	var $__decorators;
	var $html_renderer;
	function Component($registered_callbacks=array()) {
		$app = $this->application();
		if (isset($registered_callbacks))
			$this->registered_callbacks = $registered_callbacks;
		$this->configuration=array('use_component_namemangling' => false,
		                           'use_action_namemangling' => false,
		                           'backtrackable_objects' => array());
		$this->configuration = array_merge($this->configuration, $this->configure());

		$this->listeners = array();
        $this->__children = array();
		$this->__decorators = array();
		$this->__actions =& $this->declare_actions();
		$this->html_renderer =& new HtmlRenderer($this);
	}

	function start() {}
	function configure() {
		return array();
	}
	function &application() {
		return Application::instance();
	}
	function backtrackable_objects() {
		return array();
	}
	function declare_actions() {
		return array();
	}

	function add_decorator(&$decorator) {
		$this->__decorators[] =& $decorator;
    }

	function copy_for_backtracking() {
		/* Use clone from PEAR PHP_Compat */
	}

	function localized($string) {
		return $string;
	}

	function call_action($action_selector, $params) {
		if (!in_array($action_selector, $this->__actions))
			return false;

		eval('$this->' . $action_selector . '($params);');
		$this->triggerEvent($action_selector . '_action');
		return true;
	}

	function &render_action_link(&$action_selector,$params=array()) {
        $component_renderer =& ComponentRenderer::getInstance();
        $action =& new Action($this, $action_selector, $params);
        return $component_renderer->render_action_link($action);
    }

    function &renderAction(&$action) {
    	$this->render_action_link($action);
    }

	function add_component(&$component, $index) {
		$this->__children[$index] =& new ComponentHolder($component,$index);
	}

	function &component_at($index) {
		$holder =& $this->__children[$index];
		return $holder->component;
	}

	function notify($message, $callback_action='notification_accepted') {
		$this->call(new NotificationDialog($message, array('callback' => $callback_action)));
	}

	function question($message, $callback_action='question_confirmed') {
		$this->call(new QuestionDialog($message, array('callback' => $callback_action)));
	}

	function render_on(&$html) {} /* Do nothing by default */
	function render() {} /* Fake */
    function renderAll(&$html) {
        foreach ($this->__decorators as $decorator) {
            $decorator->renderAll($html);
        }
        $this->render_on(&$html);
    }
    function renderContent(&$html) {
        $component_renderer =& ComponentRenderer::getInstance();
        $component_renderer->render($this, &$html);
    }

	function call(&$component) {
		// Give control to $component
		$component->listener =& $this;
		$this->holder->hold($component);
		$component->start();
	}

	function invalid_callback($callback) {
		$app =& $this->application();
		$app->invalid_callback($callback);
	}

	function callback($callback=null, $parameters=array()) {
		if ($callback == null) {
			$this->holder->hold($this->listener);
		}
		else {
			if ($this->registered_callbacks[$callback] == null) {
				$this->invalid_callback($callback);
			}
			else {
				$this->holder->hold($this->listener);
				eval('$this->listener->' . $this->registered_callbacks[$callback] . '($parameters);');
			}
		}
	}
}
?>