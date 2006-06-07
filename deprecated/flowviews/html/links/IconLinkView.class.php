<?php

require_once dirname(__FILE__) . '/LinkView.class.php';

class IconLinkView extends LinkView
{

    function renderLink($params, &$html) {
       $html->text("<a href=\"" . $params['link'] . "\">\n<img title=\"". $params['title'] . "\" src=\"". icons_url . $params['icon'] . "\" /></a>");
    }

    function renderSaveLink($link, &$html) {
        return $this->renderLink(array('link' => $link,
                                      'title' => 'Save',
                                      'icon' => 'stock_save.png'), $html);
    }

    function  renderAddLink($link, &$html) {
        return $this->renderLink(array('link' => $link,
                                      'title' => 'Add',
                                      'icon' => 'stock_new.png'), $html);

    }
    function renderShowLink($link, &$html) {
        return $this->renderLink(array('link' => $link,
                                       'title' => 'Show',
                                       'icon' => 'stock_show.png'), $html);
    }

    function renderEditLink($link, &$html) {
        return $this->renderLink(array('link' => $link,
                                       'title' => 'Show',
                                       'icon' => 'stock_edit.png'), $html);
    }

/*
    function renderNextLink($link, &$html) {
         return $this->renderLink(array('link' => $link,
                                      'title' => 'Next',
                                      'icon' => 'stock_right.png'), $html);
    }
    function renderBackActionLink($link, &$html) {
         return $this->renderActionLink(array('link' => $link,
                                      'title' => 'Back',
                                      'icon' => 'stock_left.png'), $html);
    }

    function renderSearchActionLink($link, &$html) {
         return $this->renderActionLink(array('link' => $link,
                                      'title' => 'Search',
                                      'icon' => 'stock_search.png'), $html);

    }
    function renderEditLink($link, &$html) {
        return $this->renderActionLink(array('link' => $link,
                                      'title' => 'Edit',
                                      'icon' => 'stock_edit.png'), &$html);
    }

    function renderDeleteLink($link, &$html) {
        return $this->renderActionLink(array('link' => $link,
                                      'title' => 'Delete',
                                      'icon' => 'stock_delete.png'), &$html);
    }

    function renderAddChildLink($link, &$html) {
        return $this->renderActionLink(array('link' => $link,
                                      'title' => 'Add',
                                      'icon' => 'stock_new-16.png'));
    }

    */
}

?>