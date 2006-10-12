<?php

$d = dirname(__FILE__);

require_once $d.'/DefaultCMS.class.php';
require_once $d.'/Administrator/RolesController.class.php';
require_once $d.'/Administrator/DBController.class.php';
require_once $d.'/Administrator/TablesChecker.class.php';
require_once $d.'/Administrator/TableCheckAction.class.php';
require_once $d.'/Menu.class.php';
require_once $d.'/DefaultCMSApplication.class.php';
require_once $d.'/Files/FileLoader.class.php';
require_once $d.'/Files/ReadFile.class.php';
require_once $d.'/CMS/CollectionNavigator.class.php';
require_once $d.'/CMS/CollectionViewer.class.php';
require_once $d.'/CMS/PersistentObjectPresenter.class.php';
require_once $d.'/CMS/PersistentObjectViewer.class.php';
require_once $d.'/CMS/PersistentObjectEditor.class.php';
require_once $d.'/CMS/FieldEditor.class.php';
require_once $d.'/CMS/ValidationErrorsDisplayer.class.php';
require_once $d.'/CMS/PresentersFactory/FieldPresenterFactory.class.php';
require_once $d.'/CMS/PresentersFactory/Editors/EditorFactory.class.php';
require_once $d.'/CMS/PresentersFactory/Editors/CollectionFieldEditorFactory.class.php';
require_once $d.'/CMS/PresentersFactory/Editors/IdFieldEditorFactory.class.php';
require_once $d.'/CMS/PresentersFactory/Editors/VersionFieldEditorFactory.class.php';
require_once $d.'/CMS/PresentersFactory/Editors/IndexFieldEditorFactory.class.php';
require_once $d.'/CMS/PresentersFactory/Editors/TextAreaEditorFactory.class.php';
require_once $d.'/CMS/PresentersFactory/Editors/BoolFieldEditorFactory.class.php';
require_once $d.'/CMS/PresentersFactory/Searchers/SearcherFactory.class.php';
require_once $d.'/CMS/PresentersFactory/Searchers/TextFieldSearcherFactory.class.php';
require_once $d.'/CMS/PresentersFactory/Viewers/ViewerFactory.class.php';
require_once $d.'/CMS/PresentersFactory/Viewers/IndexFieldViewerFactory.class.php';
require_once $d.'/CMS/PresentersFactory/Viewers/BoolFieldViewerFactory.class.php';
require_once $d.'/CMS/PresentersFactory/Viewers/TextAreaViewerFactory.class.php';
require_once $d.'/CMS/PresentersFactory/Viewers/CollectionFieldViewerFactory.class.php';

require_once $d.'/CMS/IndexFieldChooser.class.php';
require_once $d.'/CMS/CollectionElementChooser.class.php';
require_once $d.'/CMS/CollectionFilterer.class.php';
require_once $d.'/Users/Login.class.php';
require_once $d.'/Users/Logout.class.php';
require_once $d.'/Users/AddUser.class.php';
require_once $d.'/Users/ModUser.class.php';
require_once $d.'/Templates/Handlers.class.php';
require_once $d.'/QuicKlick/QuicKlickComponent.class.php';
?>