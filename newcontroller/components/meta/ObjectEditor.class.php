<?php

require_once dirname(__FILE__) . '/../../FlowController.class.php';
require_once dirname(__FILE__) . '/FieldsEmbeddingVisitor.class.php';

class ObjectEditor extends FlowController
{
  function ObjectEditor(&$model, &$view, &$config) {
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
    	$collection_editor =& $this->config->editorFor($field->collection);
        $this->addComponent($collection_editor);
    }

    function embedIdField(&$field) {}

  
    function embedIndexField(&$field) {
    	$index_editor =& $this->config->editorFor($field->collection);
        $this->addComponent($index_editor);
    }

   function declare_actions() {
      return array('save', 'cancel');
    }

    /* Actions rendering */

    function renderActions(&$html) {
    	$this->renderSaveAction($html);
        $this->renderCancelAction($html);
    }

    function renderSaveAction(&$html) {
    	$action =& new FlowAction($this, 'save');
        $link = $this->action_renderer->getSaveActionLink(&$action);
        $this->view->renderSaveAction($link, $html);
    }

    function renderCancelAction(&$html) {
      $action =& new FlowAction($this, 'cancel');
        $link = $this->action_renderer->getCancelActionLink(&$action);
        $this->view->renderCancelAction($link, $html);
    }

    function save() {
      $this->callback('on_save');
    }

    function cancel() {
      $this->callback('on_cancel');
    }

}


?>