<?php

require_once dirname(__FILE__) . '/Configuration.class.php';

class ObjectDisplayerActionsConfiguration extends Configuration
{
     function renderActions(&$displayer,&$html) {
        $action_renderer =& $this->buildActionRenderer();
        $displayer->renderEditAction($action_renderer, $html);
     }
}

?>