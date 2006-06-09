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
require_once 'CMS/ComponentFactory/FieldPresenterFactory.class.php';
require_once 'CMS/ComponentFactory/Edit/CollectionFieldEditorFactory.class.php';
require_once 'CMS/ComponentFactory/Edit/EditorFactory.class.php';
require_once 'CMS/ComponentFactory/Edit/IdFieldEditorFactory.class.php';
require_once 'CMS/ComponentFactory/Edit/IndexFieldEditorFactory.class.php';
require_once 'CMS/ComponentFactory/Edit/TextAreaEditorFactory.class.php';
require_once 'CMS/ComponentFactory/Edit/BoolFieldEditorFactory.class.php';
require_once 'CMS/ComponentFactory/Search/SearchComponentFactory.class.php';
require_once 'CMS/ComponentFactory/Search/TextFieldSearchComponentFactory.class.php';
require_once 'CMS/ComponentFactory/Show/IndexFieldViewerFactory.class.php';
require_once 'CMS/ComponentFactory/Show/BoolFieldViewerFactory.class.php';
require_once 'CMS/ComponentFactory/Show/ViewerFactory.class.php';
require_once 'CMS/CollectionNavigator.class.php';
require_once 'CMS/IndexFieldChooser.class.php';
require_once 'CMS/CollectionElementChooser.class.php';
require_once 'CMS/FilterCollectionComponent.class.php';
require_once 'CMS/PersistentObjectPresenter.class.php';
require_once 'Users/Login.class.php';
require_once 'Users/Logout.class.php';
require_once 'Users/AddUser.class.php';
require_once 'Users/ModUser.class.php';
?>