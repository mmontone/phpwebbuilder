<?php

require_once dirname(__FILE__) . '/Configuration.class.php';

class ObjectEditorActionsConfiguration extends Configuration
{
     function renderActions(&$displayer,&$html) {
        $action_renderer =& $this->buildActionRenderer();
        $displayer->renderSaveAction($action_renderer, $html);
        $displayer->renderCancelAction($action_renderer, $html);
     }
}


?>