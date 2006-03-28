<?php

require_once dirname(__FILE__) . '/../../FlowController.class.php';

class CollectionDisplayer extends FlowController
{
  var $page;
  var $page_size;

  function CollectionDisplayer(&$collection, &$view, &$config) {
    parent::FlowController($collection, $view, $config);
        $this->page_size = 10;
        $this->page = 1;
        $this->model->limit = $this->page_size;
  }

  function declare_actions() {
    return array('sort_by', 'next', 'previous');
  }

  function sortLinkFor($field_name) {
    return $this->render_action_link('sort_by', array('field_name' => $field_name));
  }

  function getElements() {
    $this->model->offset = $this->page;
    $this->model->limit = $this->page_size;
    return $this->model->objects();
  }
  
  function sort_by($params) {
    $this->model->oreder = $params['field_name'];
    $this->render();
  }

    function next() {
      $this->page++;
      $this->render();
    }
    
    function previous() {
      $this->page--;
      $this->render();
    }

    function see_more() {
      $this->page_size *= 2;
    }

    function see_less() {
      $this->page_size /= 2;
    }

    function getSeeLessActionLink() {
      return $this->render_action_link('see_less');
    }

    function getActionsFor(&$element) {
        $user =& User::getInstance();
        $user_actions = $user->getActionsFor($element);
        return array_merge($this->elements_actions, $user_actions);
    }
}

class PersistentCollectionDisplayer extends CollectionDisplayer {}
?>