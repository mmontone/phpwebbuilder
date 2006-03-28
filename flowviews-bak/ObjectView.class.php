<?php

class ObjectView extends PWBObject
{
    var $model;
    var $layout;
    var $field_view_renderer;

    function ObjectView(&$model, &$layout, &$field_view_renderer) {
        $this->model =& $model;
        $this->layout =& $layout;
        $this->field_view_renderer =& $field_view_renderer;
    }

    function render(&$action_renderer, &$html) {
        $this->beginObjectRendering($html);
      	$this->layout->beginObjectTitleRendering($html);
        $html->text('<h1>' . $this->renderTitle() . '</h1>');
        $this->layout->endObjectTitleRendering($html);
        foreach ($this->model->allFields() as $field) {
    		$this->layout->beginFieldRendering($field, $html);
            $this->field_view_renderer->render($field, $action_renderer, $html);
            $this->layout->endFieldRendering($field, $html);
    	}
        $this->layout->endObjectRendering($html);
    }

    function renderTitle() {
        $this->model->get_class();
    }

    function beginObjectRendering(&$html) {
    	$this->triggerEvent('about_to_begin_object_rendering', array('view' => $this,
                                                                     'html' => $html));
      	$this->layout->beginObjectRendering($html);
    }

    function endObjectRendering(&$html) {
    	$this->layout->beginObjectRendering($html);
        $this->triggerEvent('object_rendered', array('view' => $this,
                                                     'html' => $html));
    }

    function endFieldRendering(&$field, &$html) {
    	$this->layout->endFieldRendering(&$html);
        $this->triggerEvent('field_rendered', array('view' => $this,
                                                    'html' => $html,
                                                    'field' => $field));
    }
}

?>