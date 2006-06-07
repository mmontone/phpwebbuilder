<?php

require_once dirname(__FILE__) . '/../../../FieldView.class.php';

class CollectionFieldEditView extends FieldView
{
    function CollectionFieldEditView(&$field, $config) {
        parent::FieldView($field, $config);
    }

    function render(&$html) {
    	$this->config->renderCollectionField($field, $html);
    }
}
?>