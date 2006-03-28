<?php

require_once "LinkView.class.php";

class TextLinkView extends LinkView
{
    function renderLink($params, &$html) {
        $link = $params[$link];
        $title = $params[$title];

        $html->text("<a href=\"$link\">$title</a>");
    }

    function renderSaveLink($link, &$html) {
        $this->renderLink(array('link' => $link,
                                      'title' => 'Save'), $html);
    }

    function  renderAddLink($link, &$html) {
        $this->renderLink(array('link' => $link,
                                      'title' => 'Add'), $html);

    }
    function renderShowLink($link, &$html) {
        $this->renderLink(array('link' => $link,
                                       'title' => 'Show'), $html);
    }

    function renderNextLink($link, &$html) {
        $this->renderLink(array('link' => $link,
                                      'title' => 'Next'), $html);
    }
    function renderBackActionLink($link, &$html) {
        $this->renderActionLink(array('link' => $link,
                                      'title' => 'Back'), $html);
    }

    function renderSearchActionLink($link, &$html) {
        $this->renderActionLink(array('link' => $link,
                                      'title' => 'Search'), $html);

    }
    function renderEditLink($link, &$html) {
        $this->renderActionLink(array('link' => $link,
                                      'title' => 'Edit'), &$html);
    }

    function renderDeleteLink($link, &$html) {
        $this->renderActionLink(array('link' => $link,
                                      'title' => 'Delete'), &$html);
    }

    function renderAddChildLink($link, &$html) {
        $this->renderActionLink(array('link' => $link,
                                      'title' => 'Add'));
    }
}

?>