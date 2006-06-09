<?php

require_once 'DefaultCMS.class.php';
require_once 'Administrator/RolesController.class.php';
require_once 'Administrator/DBController.class.php';
require_once 'Administrator/TablesChecker.class.php';
require_once 'Menu.class.php';
require_once 'DefaultCMSApplication.class.php';
require_once 'Files/FileLoader.class.php';
require_once 'CMS/CollectionViewer.class.php';
require_once 'CMS/PersistentObjectViewer.class.php';
require_once 'CMS/PersistentObjectEditor.class.php';
require_once 'CMS/ValidationErrorsDisplayer.class.php';
require_once 'CMS/PresentersFactory/FieldPresenterFactory.class.php';
require_once 'CMS/PresentersFactory/Editors/CollectionFieldEditorFactory.class.php';
require_once 'CMS/PresentersFactory/Editors/EditorFactory.class.php';
require_once 'CMS/PresentersFactory/Editors/IdFieldEditorFactory.class.php';
require_once 'CMS/PresentersFactory/Editors/IndexFieldEditorFactory.class.php';
require_once 'CMS/PresentersFactory/Editors/TextAreaEditorFactory.class.php';
require_once 'CMS/PresentersFactory/Editors/BoolFieldEditorFactory.class.php';
require_once 'CMS/PresentersFactory/Searchers/SearcherFactory.class.php';
require_once 'CMS/PresentersFactory/Searchers/TextFieldSearcherFactory.class.php';
require_once 'CMS/PresentersFactory/Viewers/IndexFieldViewerFactory.class.php';
require_once 'CMS/PresentersFactory/Viewers/BoolFieldViewerFactory.class.php';
require_once 'CMS/PresentersFactory/Viewers/ViewerFactory.class.php';
require_once 'CMS/CollectionNavigator.class.php';
require_once 'CMS/IndexFieldChooser.class.php';
require_once 'CMS/CollectionElementChooser.class.php';
require_once 'CMS/CollectionFilterer.class.php';
require_once 'CMS/PersistentObjectPresenter.class.php';
require_once 'Users/Login.class.php';
require_once 'Users/Logout.class.php';
require_once 'Users/AddUser.class.php';
require_once 'Users/ModUser.class.php';
?>