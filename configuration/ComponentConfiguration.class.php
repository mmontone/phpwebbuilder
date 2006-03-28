<?php

require_once dirname(__FILE__) . '/Configuration.class.php';

class ComponentConfiguration extends Configuration
{
    var $hidden_fields;
    var $disabled_actions;

    function ComponentConfiguration($config=array()) {
        parent::Configuration($config);
        $this->hidden_fields = array();
    }

    function &getLayout() {
        if ($this->layout)
            return $this->layout;

        $layout_class = $this->config['layout'] . 'Layout';
        $this->layout =& new $layout_class;
        return $this->layout;
    }

    function &getLinkView() {
        if ($this->link_view)
            return $this->link_view;
        $link_view_class = $this->config['link_view'] . 'LinkView';
        $this->link_view =& new $link_view_class;
        return $this->link_view;
    }

    function &getActionRenderer() {
    	if ($this->action_renderer)
            return $this->action_renderer;
        $action_renderer_class = $this->config['action_renderer'] . 'ActionRenderer';
        $this->action_renderer =& new $action_renderer_class;
        return $this->action_renderer;
    }

    function defaultConfiguration() {
        return array('layout' => 'Div',
                     'link_view' => 'Icon',
                     'action_renderer' => 'Simple');
    }

    function &viewFor(&$object) {
        $link_view =& new $this->config['link_view'];
        $view_renderer =& new ViewRenderer($object);
        $view =& $view_renderer->getView();
        $view->layout =& new $this->config['layout'];
        return $view;
    }


    function conventionDisplayerFor(&$object) {
    	$view_class =& $this->displayerViewClassFor($object);
	    $view =& new $view_class;
        $controller_class =& $this->displayerClassFor($object);
        $controller =& new $controller_class($object, $view, $this);
        return $controller;
    }

    function conventionEditorFor(&$object) {
    	$view_class =& $this->editorViewClassFor($object);
	$view =& new $view_class;
        $controller_class = $this->editorClassFor($object);
        $controller =& new $controller_class($object, $view, $this);
        return $controller;
    }

    function displayerFor(&$object) {
        if ($this->configForChildren($object->holder->owner_index, $component_config)) {
            return $component_config->displayerFor($object, $this);
        }

        if ($this->configForPath("", $component_config)) {
    	   return $component_config->displayerFor($object, $this);
        }

        if ($this->configForClass(get_class($object), $component_config)) {
    	   return $component_config->displayerFor($object, $this);
        }

	   return $this->conventionDisplayerFor(&$object);
    }

    function editorFor(&$object) {
        if ($this->configForChildren($object->holder->owner_index, $component_config)) {
            return $component_config->editorFor($object, $this);
        }

        if ($this->configForPath("", $component_config)) {
    	   return $component_config->editorFor($object, $this);
        }

        if ($this->configForClass(get_class($object), $component_config)) {
    	   return $component_config->editorFor($object, $this);
        }

        return $this->conventionEditorFor(&$object);
    }



    function classFor(&$object, $postfix, $else_factory) {
        $factory_name = get_class($object) . $postfix;
        if ($this->config[$factory_name])
            return $this->config[$factory_name];

        if (class_exists($factory_name))
            return $factory_name;

        return $else_factory;
    }


    function displayerClassFor(&$object) {
    	 return $this->classFor($object, 'Displayer', 'ObjectDisplayer');
    }

    function displayerViewClassFor(&$object) {
        return $this->classFor($object, 'DisplayerView', 'ObjectDisplayerView');
    }

    function editorClassFor(&$object) {
        return $this->classFor($object, 'Editor', 'ObjectEditor');
    }

    function editorViewClassFor(&$object) {
        return $this->classFor($object, 'EditorView', 'ObjectEditorView');
    }
}

?>