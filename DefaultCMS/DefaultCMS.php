<?php

$d = dirname(__FILE__);

compile_once ($d.'/DefaultCMS.class.php');
compile_once ($d.'/Administrator/RolesController.class.php');
compile_once ($d.'/Administrator/DBController.class.php');
compile_once ($d.'/Administrator/TablesChecker.class.php');
compile_once ($d.'/Administrator/TableCheckAction.class.php');
compile_once ($d.'/Menu.class.php');
compile_once ($d.'/DefaultCMSApplication.class.php');
compile_once ($d.'/Files/FileLoader.class.php');
compile_once ($d.'/Files/ReadFile.class.php');
compile_once ($d.'/CMS/CollectionNavigator.class.php');
compile_once ($d.'/CMS/CollectionViewer.class.php');
compile_once ($d.'/CMS/PersistentObjectPresenter.class.php');
compile_once ($d.'/CMS/PersistentObjectViewer.class.php');
compile_once ($d.'/CMS/PersistentObjectEditor.class.php');
compile_once ($d.'/CMS/FieldEditor.class.php');
compile_once ($d.'/CMS/ValidationErrorsDisplayer.class.php');
compile_once ($d.'/CMS/PresentersFactory/FieldPresenterFactory.class.php');
compile_once ($d.'/CMS/PresentersFactory/Editors/EditorFactory.class.php');
compile_once ($d.'/CMS/PresentersFactory/Editors/CollectionFieldEditorFactory.class.php');
compile_once ($d.'/CMS/PresentersFactory/Editors/IdFieldEditorFactory.class.php');
compile_once ($d.'/CMS/PresentersFactory/Editors/VersionFieldEditorFactory.class.php');
compile_once ($d.'/CMS/PresentersFactory/Editors/IndexFieldEditorFactory.class.php');
compile_once ($d.'/CMS/PresentersFactory/Editors/TextAreaEditorFactory.class.php');
compile_once ($d.'/CMS/PresentersFactory/Editors/BoolFieldEditorFactory.class.php');
compile_once ($d.'/CMS/PresentersFactory/Editors/DateTimeFieldEditorFactory.class.php');
compile_once ($d.'/CMS/PresentersFactory/Searchers/SearcherFactory.class.php');
compile_once ($d.'/CMS/PresentersFactory/Searchers/TextFieldSearcherFactory.class.php');
compile_once ($d.'/CMS/PresentersFactory/Viewers/ViewerFactory.class.php');
compile_once ($d.'/CMS/PresentersFactory/Viewers/IndexFieldViewerFactory.class.php');
compile_once ($d.'/CMS/PresentersFactory/Viewers/BoolFieldViewerFactory.class.php');
compile_once ($d.'/CMS/PresentersFactory/Viewers/TextAreaViewerFactory.class.php');
compile_once ($d.'/CMS/PresentersFactory/Viewers/CollectionFieldViewerFactory.class.php');

compile_once ($d.'/CMS/IndexFieldChooser.class.php');
compile_once ($d.'/CMS/CollectionElementChooser.class.php');
compile_once ($d.'/CMS/CollectionFilterer.class.php');
compile_once ($d.'/Users/Login.class.php');
compile_once ($d.'/Users/Logout.class.php');
compile_once ($d.'/Users/AddUser.class.php');
compile_once ($d.'/Users/ModUser.class.php');
compile_once ($d.'/Templates/Handlers.class.php');
compile_once ($d.'/QuicKlick/QuicKlickComponent.class.php');
?>