<?php

$d = dirname(__FILE__);

require_once $d.'/PageRenderer.class.php';
require_once $d.'/XULPageRenderer.class.php';
require_once $d.'/templates/ViewCreator.class.php';
require_once $d.'/templates/XMLParser.class.php';
require_once $d.'/XML/DOMXMLNode.class.php';
require_once $d.'/XML/XMLModification.class.php';
require_once $d.'/XML/XMLNode.class.php';
require_once $d.'/XML/XMLNodeModificationsTracker.class.php';
require_once $d.'/DefaultViews/HTML/HTMLDefaultView.class.php';
require_once $d.'/DefaultViews/XUL/XULDefaultView.class.php';
require_once $d.'/templates/HTMLContainer.class.php';
require_once $d.'/templates/HTMLTemplate.class.php';
require_once $d.'/NullView.class.php';
require_once $d.'/XML/XMLTextNode.class.php';
require_once $d.'/Handler/Handler.php';
?>
