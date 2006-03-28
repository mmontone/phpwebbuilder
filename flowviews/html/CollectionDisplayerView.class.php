<?php

require_once dirname(__FILE__) . '/HTMLCollectionView.class.php';

class CollectionDisplayerView extends HTMLCollectionView
{

    function getElements() {
      $this->controller->getElements();
    }

    function beginElementsRendering(&$html) {
      $super->beginElementsRendering($html);  
      $this->renderSortingBar($html);
    }

    
    function renderElement(&$element, &$html) {
      foreach ($element->allFields() as $field) {
        if ($field->is_index) {
          $this->renderElementField($field, $html);
        }
      }
    }

    function renderElementField($field, $html) {
      $this->beginElementFieldRendering($html);
      $view =& $this->config->showViewFor($field);
      $view->render_on($html);
      $this->endElementFieldRendering($html);
    }
    
    function renderSortingBar(&$html) {
      $this->beginSortingBarRendering($html);
      $element_info = new $this->model->datatype;
      foreach ($element_info->allFields() as $field) {
        if ($field->is_index) {
          $this->renderSortAction($field->name, $html);
        }
      }
      $this->endSortingBarRendering($html);
    }

    function renderSortAction($field_name, &$html) {
      $link =$this->controller->getSortActionLink($field_name);
      $html->a($link, $field_name);
    }

    function renderActions(&$html) {
      $this->renderSeeLessAction($html);
      $this->renderPreviousAction($html);
      $this->renderNextAction($html);
      $this->renderSeeMoreAction($html);
    }

    function renderSeeLessAction(&$html) {
      if ($this->controller->pageSize > $this->less_limit) {
        $link = $this->controller->getSeeLessActionLink();
        $html->a($link, $this->config->translate('see less'));
      }
    }
}

class PersistentCollectionDisplayerView extends CollectionDisplayerView {}

?>