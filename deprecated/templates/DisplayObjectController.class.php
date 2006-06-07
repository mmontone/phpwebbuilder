<?php

require_once dirname(__FILE__) . '/../Controllers/Controller.class.php';

class DisplayObjectController extends Controller
{
  function start($params) {
    $this->loadModel($params);
    $this->loadView($params);
    return $this->view->render();
  }

  function loadModel($params) {
    $this->aboutToLoadModel($params);

    assert($params['datatype']);
    assert($params['id']);

    $this->model =& new $params['datatype'];
    $this->model->setID($params['id']);
    $this->model->load();
  }

  function aboutToLoadModel(&$params){}

  function dispatchActions() {}
}

?>
