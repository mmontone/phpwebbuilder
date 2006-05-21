<?php

require_once dirname(__FILE__) . '/../../FlowController.class.php';
require_once dirname(__FILE__) . '/FieldsEmbeddingVisitor.class.php';

class ObjectDisplayer extends FlowController
{
	var $fields_embedding_visitor;

	/* Initialization */

    function ObjectDisplayer(&$model, &$view, &$config)
    {
    	parent::FlowController($model, $view, $config);
        $this->fields_embedding_visitor =& new FieldsEmbeddingVisitor($this);
        $this->embedFields();
    }

    /* Embedding */

    function embedFields() {
        foreach ($this->model->allFields() as $field) {
            $field->visit($this->fields_embedding_visitor);
        }
    }

    function embedTextField(&$field) {
    	/* Don't embed */
    }

    function embedCollectionField(&$field) {
    	$collection_displayer =& $this->config->displayerFor($field->collection);
        var_dump($collection_displayer);
        $this->addComponent($collection_displayer);
    }

    function embedIdField(&$field) {}

    function &collectionDisplayerFor(&$collection) {
    	return $this->config->collectionDisplayerFor($collection);
    }

    function embedIndexField(&$field) {
    	$index_displayer =& $this->indexDisplayerFor($field->collection);
        $this->addChildren($index_displayer);
    }

    function &indexDisplayerFor(&$collection) {
    	return $this->config->newIndexDisplayer($collection);
    }


    /* Actions rendering */

    function renderActions(&$html) {
      $user =& set_single_instance_of('User');
      foreach ($this->actions as $action) {
        if ($user->isAbleTo($action, $this->model))
          $this->renderAction($action, $html);
      }
    }

    function renderAction($action, &$html) {
    	$flow_action =& new FlowAction($this, $action);
        $method = 'get' . $action . 'ActionLink';
        $link = $this->action_renderer->$method(&$flow_action);
        $method = 'render' . $action . 'Action';
        $this->view->$method($link, $html);
    }

    function declare_actions() {
      return array('edit');
    }

    function edit() {
      $editor =& $this->config->editorFor($this->model);
      $editor->registerCallbacks(array('on_cancel' => callback($this, 'editionCanceled'),
                                       'on_save' => callback($this, 'saveEditedObject')));
      $this->call($editor);
    }

    function saveEditedObject($params) {
      $this->render();
    }

    function editionCanceled() {
      $this->render();
    }

}





/*
class CollectionEmbedder
{
	var $component;

    function CollectionFieldEmbedder(&$component) {
    	$this->component =& $component;
    }

    function embedField(&$field) {
        $this->component->addChildren($field->name, $component->config->getCollectionDisplayer());
    }

    function renderField(&$field, &$html) {
		$component =& $this->component->childrenAt($field->name);
        $component->renderContent($html);
	}

    function renderActions(&$html)  {

    }



}

class CollectionDisplayer
{
    function embedField(&$field) {
        $this->component->addChildren($field->name, $component->config->getCollectionDisplayer());
    }

    function renderField(&$field) {
    	$component =& $this->component->childrenAt($field->name);
        $component->disableActions(array('add', 'edit'));
        $component->renderContent($html);
        $this->renderAction('add_' . $field->name . '_element');
        $this->renderAction('edit_' . $field->name);
    }
}
*/
?>