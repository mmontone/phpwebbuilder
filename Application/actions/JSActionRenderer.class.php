<?php
require_once dirname(__FILE__) . '/PlainActionRenderer.class.php';

class JSFlowActionRenderer extends PlainActionRenderer
{
    function JSFlowActionRenderer() {
    }

    function getJSQuestionLink($action, $msg, &$html) {
        $this->addJSQuestionScript($msg, $html);
        $translator =& single_instance_of('Translator');
        $translated_msg = $translator->translate($msg);
        return "javascript:question_dialog(\"$action\",\"$translated_msg\");";
    }

    function getSaveActionLink() {
        $this->getJSQuestionLink($this->renderActionLink('save'), 'Are you sure you want to save?', &$html);
    }

    function getDeleteActionLink() {
        $this->getJSQuestionLink($this->renderActionLink('delete'), 'Are you sure you want to delete?', &$html);
    }
}

?>