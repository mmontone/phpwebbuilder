<?php

$d = dirname(__FILE__);

compile_once ($d.'/PageRenderer.class.php');
compile_once ($d.'/XULPageRenderer.class.php');
compile_once ($d.'/templates/ViewCreator.class.php');
compile_once ($d.'/templates/XMLParser.class.php');
compile_once ($d.'/XML/DOMXMLNode.class.php');
compile_once ($d.'/XML/XMLModification.class.php');
compile_once ($d.'/XML/XMLNode.class.php');
compile_once ($d.'/XML/XMLNodeModificationsTracker.class.php');
compile_once ($d.'/DefaultViews/HTML/HTMLDefaultView.class.php');
compile_once ($d.'/DefaultViews/XUL/XULDefaultView.class.php');
compile_once ($d.'/templates/HTMLContainer.class.php');
compile_once ($d.'/templates/HTMLTemplate.class.php');
compile_once ($d.'/NullView.class.php');
compile_once ($d.'/XML/XMLTextNode.class.php');
compile_once ($d.'/Handler/Handler.php');



compile_once ($d.'/Handler/ViewHandler.class.php');
compile_once ($d.'/Handler/HTMLHandler/HTMLHandler.class.php');
compile_once ($d.'/Handler/HTMLHandler/WidgetHTMLHandler.class.php');
compile_once ($d.'/Handler/HTMLHandler/TextHTMLHandler.class.php');
compile_once ($d.'/Handler/HTMLHandler/TextAreaComponentHTMLHandler.class.php');
compile_once ($d.'/Handler/HTMLHandler/LinkHTMLHandler.class.php');
compile_once ($d.'/Handler/HTMLHandler/JSCommandLinkHTMLHandler.class.php');
compile_once ($d.'/Handler/HTMLHandler/HTMLHTMLHandler.class.php');
compile_once ($d.'/Handler/HTMLHandler/CommandLinkHTMLHandler.class.php');
compile_once ($d.'/Handler/HTMLHandler/NavigationLinkHTMLHandler.class.php');
compile_once ($d.'/Handler/HTMLHandler/CheckBoxHTMLHandler.class.php');
compile_once ($d.'/Handler/HTMLHandler/InputHTMLHandler.class.php');
compile_once ($d.'/Handler/HTMLHandler/WikiComponentHTMLHandler.class.php');
compile_once ($d.'/Handler/HTMLHandler/DateInputHTMLHandler.class.php');
compile_once ($d.'/Handler/HTMLHandler/RadioButtonHTMLHandler.class.php');
compile_once ($d.'/Handler/HTMLHandler/SelectHTMLHandler.class.php');
compile_once ($d.'/Handler/HTMLHandler/SelectMultipleHTMLHandler.class.php');
compile_once ($d.'/Handler/HTMLHandler/FilenameHTMLHandler.class.php');
compile_once ($d.'/Handler/HTMLHandler/PasswordHTMLHandler.class.php');

compile_once ($d.'/Handler/XULHandler/XULHandler.class.php');
compile_once ($d.'/Handler/XULHandler/WidgetXULHandler.class.php');
compile_once ($d.'/Handler/XULHandler/SelectXULHandler.class.php');
compile_once ($d.'/Handler/XULHandler/SelectMultipleXULHandler.class.php');
compile_once ($d.'/Handler/XULHandler/RadioButtonXULHandler.class.php');
compile_once ($d.'/Handler/XULHandler/CommandLinkXULHandler.class.php');
compile_once ($d.'/Handler/XULHandler/LinkXULHandler.class.php');
compile_once ($d.'/Handler/XULHandler/CheckBoxXULHandler.class.php');
compile_once ($d.'/Handler/XULHandler/TextXULHandler.class.php');
compile_once ($d.'/Handler/XULHandler/TextAreaComponentXULHandler.class.php');
compile_once ($d.'/Handler/XULHandler/InputXULHandler.class.php');

?>