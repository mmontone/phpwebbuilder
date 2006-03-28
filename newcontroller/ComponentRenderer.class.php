<?php
require_once dirname(__FILE__) . '/Application.class.php';

$ComponentRenderer_instance = null;

class ComponentRenderer
{
    var $rendering_chain;
    //static $instance;

    function ComponentRenderer() {
        $this->rendering_chain = array();
    }

    function &getInstance() {
/*        if (ComponentRenderer::$instance == null) {
            ComponentRenderer::$instance = new ComponentRenderer();
        }
        return ComponentRenderer::$instance;*/
        global $ComponentRenderer_instance;
        if ($ComponentRenderer_instance == null) {
            $ComponentRenderer_instance = new ComponentRenderer();
        }
        return $ComponentRenderer_instance;
    }

    function render_action_link(&$action) {
        $app =& Application::instance();
        return $app->render_action_link($action);
    }

    function render(&$component, &$html) {
        array_push($this->rendering_chain, $component);
        $comp =& ComponentRenderer::getInstance();
        $component->renderAll(&$html);
        array_pop($this->rendering_chain);
    }
}
?>