<?php
require_once dirname(__FILE__) . '/Application.class.php';

$instance = null;

class ComponentRenderer
{
    var $rendering_chain;

    function ComponentRenderer() {
        $this->rendering_chain = array();
    }

    function &getInstance() {
    	global $instance;
        if ($instance == null) {
            $instance = new ComponentRenderer();
        }
        return $instance;
    }

    function render_action_link(&$action) {
        $app =& Application::instance();
        return $app->render_action_link($action, $this->rendering_chain);
    }

    function render($component, &$html) {
        array_push($this->rendering_chain, $component);
        $component->renderAll(&$html);
        array_pop($this->rendering_chain);
    }
}
?>