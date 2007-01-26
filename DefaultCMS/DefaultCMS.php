<?php

compile_once (dirname(__FILE__).'/DefaultCMS.class.php');
compile_once (dirname(__FILE__).'/Administrator/RolesController.class.php');
compile_once (dirname(__FILE__).'/Administrator/DBController.class.php');
compile_once (dirname(__FILE__).'/Administrator/TablesChecker.class.php');
compile_once (dirname(__FILE__).'/Administrator/FieldMapper.class.php');
compile_once (dirname(__FILE__).'/Menu.class.php');
compile_once (dirname(__FILE__).'/DefaultCMSApplication.class.php');
compile_once (dirname(__FILE__).'/Files/FileLoader.class.php');
compile_once (dirname(__FILE__).'/Files/ReadFile.class.php');
compile_once (dirname(__FILE__).'/CMS/CollectionNavigator.class.php');
compile_once (dirname(__FILE__).'/CMS/CollectionViewer.class.php');
compile_once (dirname(__FILE__).'/CMS/PersistentObjectPresenter.class.php');
compile_once (dirname(__FILE__).'/CMS/PersistentObjectViewer.class.php');
compile_once (dirname(__FILE__).'/CMS/PersistentObjectEditor.class.php');
compile_once (dirname(__FILE__).'/CMS/FieldEditor.class.php');
compile_once (dirname(__FILE__).'/CMS/ValidationErrorsDisplayer.class.php');
compile_once (dirname(__FILE__).'/CMS/PresentersFactory/FieldPresenterFactory.class.php');
compile_once (dirname(__FILE__).'/CMS/PresentersFactory/Editors/EditorFactory.class.php');
compile_once (dirname(__FILE__).'/CMS/PresentersFactory/Editors/CollectionFieldEditorFactory.class.php');
compile_once (dirname(__FILE__).'/CMS/PresentersFactory/Editors/IdFieldEditorFactory.class.php');
compile_once (dirname(__FILE__).'/CMS/PresentersFactory/Editors/VersionFieldEditorFactory.class.php');
compile_once (dirname(__FILE__).'/CMS/PresentersFactory/Editors/IndexFieldEditorFactory.class.php');
compile_once (dirname(__FILE__).'/CMS/PresentersFactory/Editors/TextAreaEditorFactory.class.php');
compile_once (dirname(__FILE__).'/CMS/PresentersFactory/Editors/BoolFieldEditorFactory.class.php');
compile_once (dirname(__FILE__).'/CMS/PresentersFactory/Editors/DateTimeFieldEditorFactory.class.php');
compile_once (dirname(__FILE__).'/CMS/PresentersFactory/Searchers/SearcherFactory.class.php');
compile_once (dirname(__FILE__).'/CMS/PresentersFactory/Searchers/TextFieldSearcherFactory.class.php');
compile_once (dirname(__FILE__).'/CMS/PresentersFactory/Viewers/ViewerFactory.class.php');
compile_once (dirname(__FILE__).'/CMS/PresentersFactory/Viewers/IndexFieldViewerFactory.class.php');
compile_once (dirname(__FILE__).'/CMS/PresentersFactory/Viewers/BoolFieldViewerFactory.class.php');
compile_once (dirname(__FILE__).'/CMS/PresentersFactory/Viewers/TextAreaViewerFactory.class.php');
compile_once (dirname(__FILE__).'/CMS/PresentersFactory/Viewers/CollectionFieldViewerFactory.class.php');

compile_once (dirname(__FILE__).'/CMS/IndexFieldChooser.class.php');
compile_once (dirname(__FILE__).'/CMS/CollectionElementChooser.class.php');
compile_once (dirname(__FILE__).'/CMS/CollectionFilterer.class.php');
compile_once (dirname(__FILE__).'/Users/Login.class.php');
compile_once (dirname(__FILE__).'/Users/Logout.class.php');
compile_once (dirname(__FILE__).'/Users/AddUser.class.php');
compile_once (dirname(__FILE__).'/Users/ModUser.class.php');
compile_once (dirname(__FILE__).'/Templates/Handlers.class.php');
compile_once (dirname(__FILE__).'/QuicKlick/QuicKlickComponent.class.php');
?>