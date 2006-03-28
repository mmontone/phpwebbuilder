<?php

require_once dirname(__FILE__) . '/ActionRenderer.class.php';

class FlowActionRenderer extends ActionRenderer
{
    var $action_link_renderer;

    function FlowLinker(&$linker_link_view, &$action_link_renderer) {
        parent::Linker($linker_link_view);
        $this->action_link_renderer = $action_link_renderer;
    }

    function renderActionLink($action) {
        $component_renderer = ComponentRenderer::getInstance();
        $action = new Action(null, $action_selector);
        return $component_renderer->render_action_link($action);
    }


    function renderAddAction(&$html) {
        $this->link_view->renderAddLink($this->renderActionLink('add'), &$html);
    }

    function renderNextAction(&$html) {
        $this->link_view->renderNextLink($this->renderActionLink('next'), &$html);
    }

    function renderPreviousAction(&$html) {
        $this->link_view->renderPreviousLink($this->renderActionLink('previous'), &$html);
    }

    function renderBackAction(&$html) {
        $this->link_view->renderBackLink($this->renderActionLink('back'), &$html);
    }

    function renderEditAction(&$html) {
        $this->link_view->renderEditLink($this->renderActionLink('edit'), &$html);
    }

    function renderSaveAction(&$html) {
        $this->link_view->renderSaveLink($this->renderActionLink('save'), &$html);
    }

    function renderShowAction(&$html) {
        $this->link_view->renderShowLink($this->renderActionLink('show'), &$html);
    }
}
?>