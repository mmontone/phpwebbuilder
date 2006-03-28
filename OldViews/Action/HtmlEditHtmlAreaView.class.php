<?php

require_once dirname(__FILE__) . '/HtmlEditFieldView.class.php';
require_once dirname(__FILE__) . '/HtmlTableEditFieldView.class.php';
require_once dirname(__FILE__) . '/../../extra/FCKeditor/fckeditor.php';


class HtmlEditHtmlAreaView extends HtmlEditFieldView
{
    function HtmlEditHtmlAreaView() {
    }

    function formObject ($object) {
        $editor =& new FCKeditor($this->frmName($object));
        $editor->BasePath = site_url . 'admin/pwb/extra/FCKeditor/';
        $editor->Value = $this->field->getValue();
        return $editor->CreateHtml();
    }
}

class HtmlTableEditHtmlAreaView extends HtmlTableEditTextAreaView
{
    function HtmlTableEditHtmlAreaView() {}

    function formObject ($object) {
        $editor =& new FCKeditor($this->frmName($object));
        $editor->BasePath = site_url . 'admin/pwb/extra/FCKeditor/';
        $editor->Value = $this->field->getValue();
        $editor->Width = '700px';
        $editor->Height = '700px';
        return $editor->CreateHtml();
    }
}

?>