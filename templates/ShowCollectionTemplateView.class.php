<?php

require_once dirname(__FILE__) . '/TemplateView.class.php';

class ShowCollectionTemplateView extends TemplateView
{
  function renderNextPageAction(&$action) {
    $this->setAction('next_page', $action);
  }

  function renderPreviousPageAction(&$action) {
    $this->setAction('previous_page', $action);
  }

  function renderEnlargePageAction(&$action) {
    $this->setAction('enlarge_page', $action);
  }

  function renderShortenPageAction(&$action) {
    $this->setAction('shorten_page', $action);
  }

  function renderSortingActions(&$sorting_actions) {
    $rendered_actions = array_map(array($this, 'renderAction'), $sorting_actions);
    $this->template_model->set('sorting_actions', $rendered_actions);
  }

  function renderAction(&$action) {
    //$action->set("template", $this->template);
    return $action->render();
  }
}

?>