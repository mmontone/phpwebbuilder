<?php

require_once dirname(__FILE__) . '/FlowView.class.php';

class CollectionView extends FlowView
{
    function CollectionView(&$collection) {
      parent::FlowView($collection);
    }

    function render(&$out) {
        $this->beginCollectionRendering($out);
        $this->beginCollectionTitleRendering($out);
        $this->renderTitle($out);
        $this->endCollectionTitleRendering($out);
        $elements = $this->getElements();
        foreach ($elements as $element) {
            $this->beginElementRendering($element, $out);
            $this->renderElement($element, $out);
            $this->endElementRendering($element, $out);
        }
        $this->endCollectionRendering($out);
    }
}

?>