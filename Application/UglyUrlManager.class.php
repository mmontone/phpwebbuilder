<?php

require_once dirname(__FILE__) . '/UrlManager.class.php';

class UglyUrlManager extends UrlManager
{
    var $url;
    var $component_nesting;

    function UglyURLManager() {
        $app =& $this->application();
    }

    function append_app_instance() {
        $app =& $this->application();
        //$this->url .= 'app_path=' . $_REQUEST['app_path'];
        $app->backbutton_manager->append_url_parameters($this->url);
    }

    function access_component() {
        $component_renderer =& ComponentRenderer::getInstance();
        $index_nesting = 1;
        foreach ($component_renderer->rendering_chain as $component) {
            $this->url .= '&comp_' . $index_nesting++ . '=' . $component->holder->owner_index();
        }
    }


    function append_action(&$action) {
        $this->url .= '&action=' . $action;
    }

    function append_parameters(&$parameters) {
        $app =& $this->application();
        foreach ($parameters as $param => $param_value) {
            $this->url .= '&p_' . $param . '=' . $param_value;
        }
    }

    function render_action_link(&$action) {
        $this->url = 'dispatch_action.php?';
        $this->append_app_instance();
        $this->access_component();
        $this->append_action($action->action_selector);
        $this->append_parameters($action->params);
        return $this->url;
    }
}

?>