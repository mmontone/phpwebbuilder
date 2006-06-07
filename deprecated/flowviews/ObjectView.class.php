<?php

require_once dirname(__FILE__) . '/FlowView.class.php';

class ObjectView extends FlowView
{
    var $hidden_fields;

    function ObjectView() {
        $this->hidden_fields = array();
    }

    function isHiddenField(&$field) {
    	return in_array($field->name, $this->hiddenFields);
    }

    function render_on(&$out) {
        $this->beginObjectRendering($out);
      	$this->beginObjectTitleRendering($out);
        $this->renderTitle($out);
        $this->endObjectTitleRendering($out);
        foreach ($this->model->allFields() as $field) {
    		$this->beginFieldRendering($field, $out);
            if (!$this->isHiddenField($field))
                $this->renderField($field, $out);
            $this->endFieldRendering($field, $out);
    	}
        $this->endObjectRendering($out);
    }
}

?>