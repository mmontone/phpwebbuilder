<?php

require_once dirname(__FILE__) . '/../../Application/PWBObject.class.php';

class ActionsView extends PWBObject
{
    var $actions;
    var $object_view;
    var $config;

    function ActionsView(&$object_view, $config) {
        $this->defaultConfig(array('layout' => $object_view->layout,
                                   'action_renderer' => $object_view->action_renderer,
                                   'check_user_permissions' => true,
                                   'actions' => $this->allActions()));
        $this->object_view = $object_view;
        $this->layout =& $this->readConfig('layout');
        $this->action_renderer =& $this->readConfig('action_renderer');
        $this->initializeActions();
        $this->object_view->addEventListener($configuration['actions_rendering_firer'], 'renderActions');
    }

    function initializeActions() {
        if ($this->readConfig('check_user_permissions')) {
        	$user =& User::instance();
            $user_actions = $user->getActionsFor($this->object_view->model);
            $this->actions = array_intersect($this->readConfig('actions'), $user_actions);
        }
        else {
            $this->actions = $this->readConfig('actions');
        }
    }

    function renderActions($params) {
    	$this->render($params['html']);
    }

    function render(&$html) {
        $this->beginActionsRendering(&$html);
        foreach ($this->actions as $action) {
            $this->beginActionRendering($html);
            $func_name = 'render' . $action . 'Link';
        	$action_renderer->$func_name(&$html);
            $this->endActionRendering($html);
        }
        $this->endActionsRendering($html);
    }

    function beginActionsRendering(&$html) {
        $this->triggerEvent('beggining_actions_rendering', array('html' => $html));
    }

    function endActionsRendering(&$html) {
        $this->triggerEvent('actions_rendered', array('html' => $html));
    }

    function endActionRendering(&$html) {
        $this->triggerEvent('action_rendered', array('html' => $html));
    }

    function beginActionRendering(&$html) {
        $this->triggerEvent('beggining_action_rendering', array('html' => $html));
    }
}

?>