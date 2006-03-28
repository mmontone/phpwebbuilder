<?php

require_once dirname(__FILE__) . '/../Controllers/Controller.class.php';

class ShowCollectionController extends Controller
{

  function start($params) {
    $this->loadModel($params);
    $this->loadView($params);
    return $this->view->render();
  }

  function nextPage($params) {
    return $this->start($params);
  }

  function previousPage($params) {
    return $this->start($params);
  }

  function enlargePage($params) {
    return $this->start($params);
  }

  function shortenPage($params) {
    return $this->start($params);
  }

  function sort($params) {
    return $this->start($params);
  }

  function loadModel($params) {
    $this->aboutToLoadModel($params);

    assert($params['datatype']);
    assert($params['limit']);

    $this->model = new PersistentCollection();
    $this->model->dataType = $params['datatype'];
    $this->model->limit = $params['limit'];
    $this->model->offset = $params['offset'];
    $this->model->conditions = $params['conditions'];
    $this->model->order = $params['order'];
  }

  function aboutToLoadModel(&$params) {}

  function dispatchActions(&$target) {
    $this->dispatchPreviousPageAction($target);
    $this->dispatchNextPageAction($target);
    $this->dispatchEnlargePageAction($target);
    $this->dispatchShortenPageAction($target);
    $this->dispatchSortingActions($target);
  }

  function dispatchPreviousPageAction(&$target) {
    if ($this->model->offset > 0) {
      $action =& $this->buildPreviousPageAction();
      $target->renderPreviousPageAction($action);
    }
  }

  function &buildPreviousPageAction() {
    $action =& $this->actionFor($this->model);
    $action->action_selector = 'previousPage';
    $action->set('offset', $this->model->offset - $this->model->limit);
    return $action;
  }

  function dispatchNextPageAction(&$target) {
    if ($this->model->offset +  $this->model->limit < $this->model->size()) {
      $action =& $this->buildNextPageAction();
      $target->renderNextPageAction($action);
    }
  }

  function &buildNextPageAction() {
    $action =& $this->actionFor($this->model);
    $action->action_selector = 'nextPage';
    $action->set('offset', $this->model->offset + $this->model->limit);
    return $action;
  }


  function dispatchShortenPageAction(&$target) {
    if ($this->model->limit > 1) {
      $action =& $this->buildShortenPageAction();
      $target->renderShortenPageAction($action);
    }
  }

  function &buildShortenPageAction() {
    $action =& $this->actionFor($this->model);
    $action->set('limit', $this->model->limit - 1);
    $action->set('offset', 0);
    $action->action_selector =  'shortenPage';
    return $action;
  }

  function dispatchEnlargePageAction(&$target) {
    if ($this->model->limit < $this->model->size()) {
      $action =& $this->buildEnlargePageAction();
      $target->renderEnlargePageAction($action);
    }
  }

  function &buildEnlargePageAction() {
    $action =& $this->actionFor($this->model);
    $action->set('limit', $this->model->limit + 1);
    $action->set('offset', 0);
    $action->action_selector = enlargePage;
    return $action;
  }

  function dispatchSortingActions(&$target) {
    $element =& new $this->model->dataType;
    $sorting_actions = array();
    foreach ($element->allFields() as $field) {
      if ($field->isIndex) {
        $action =& $this->buildSortingAction($field);
        $sorting_actions[$field->colName] = $action;
      }
    }
    if (!empty($sorting_actions))
      $target->renderSortingActions($sorting_actions);
  }

  function &buildSortingAction(&$field) {
    $action =& $this->actionFor($this->model);
    $action->set('order', 'ORDER BY ' . $field->colName);
    $action->set('offset', 0);
    $action->action_selector =  'sort';
    return $action;
  }

  function &actionFor(&$collection) {
    $action = new Action('ShowCollectionController', 'start');
    $action->set('datatype', $collection->dataType);
    $action->set('limit', $collection->limit);
    $action->set('offset', $collection->offset);
    $action->set('conditions', $collection->conditions);
    $action->set('order', $collection->order);
    $action->set('view', get_class($this->view));
    return $action;
  }
}

?>