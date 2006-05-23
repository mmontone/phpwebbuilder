<?php

require_once 'DefaultCMS.class.php';
require_once 'Administrator/RolesController.class.php';
require_once 'Administrator/DBController.class.php';
require_once 'Menu.class.php';
require_once 'DefaultCMSApplication.class.php';
require_once 'CMS/ShowCollectionComponent.class.php';
require_once 'CMS/ShowObjectComponent.class.php';
require_once 'CMS/EditObjectComponent.class.php';
require_once 'CMS/ComponentFactory/FieldComponentFactory.class.php';
require_once 'CMS/ComponentFactory/Edit/CollectionFieldEditComponentFactory.class.php';
require_once 'CMS/ComponentFactory/Edit/EditComponentFactory.class.php';
require_once 'CMS/ComponentFactory/Edit/IdFieldEditComponentFactory.class.php';
require_once 'CMS/ComponentFactory/Edit/IndexFieldEditComponentFactory.class.php';
require_once 'CMS/ComponentFactory/Edit/TextAreaEditComponentFactory.class.php';
require_once 'CMS/ComponentFactory/Search/SearchComponentFactory.class.php';
require_once 'CMS/ComponentFactory/Search/TextFieldSearchComponentFactory.class.php';
require_once 'CMS/ComponentFactory/Show/IndexFieldShowComponentFactory.class.php';
require_once 'CMS/ComponentFactory/Show/ShowComponentFactory.class.php';
require_once 'CMS/NavigationComponent.class.php';
require_once 'CMS/SelectCollectionComponent.class.php';
require_once 'CMS/FilterCollectionComponent.class.php';
require_once 'CMS/ObjectComponent.class.php';
require_once 'Users/Login.class.php';
require_once 'Users/Logout.class.php';
require_once 'Users/AddUser.class.php';
require_once 'Users/ModUser.class.php';
?>