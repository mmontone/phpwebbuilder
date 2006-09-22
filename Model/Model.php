<?php

$d = dirname(__FILE__);

require_once $d.'/Fields/DataField.class.php';
require_once $d.'/Fields/BlobField.class.php';
require_once $d.'/Fields/BoolField.class.php';
require_once $d.'/Fields/CollectionField.class.php';
require_once $d.'/Fields/ColecField.class.php';
require_once $d.'/Fields/DateTimeField.class.php';
require_once $d.'/Fields/NumField.class.php';
require_once $d.'/Fields/IndexField.class.php';
require_once $d.'/Fields/IdField.class.php';
require_once $d.'/Fields/VersionField.class.php';
require_once $d.'/Fields/SuperField.class.php';
require_once $d.'/Fields/TextField.class.php';
require_once $d.'/Fields/PasswordField.class.php';
require_once $d.'/Fields/HtmlArea.class.php';
require_once $d.'/Fields/FilenameField.class.php';
require_once $d.'/Fields/EnumField.class.php';
require_once $d.'/Fields/EmailField.class.php';
require_once $d.'/Fields/WikiArea.class.php';
require_once $d.'/DescriptedObject.class.php';
require_once $d.'/Report.class.php';
require_once $d.'/PersistentCollection.class.php';
require_once $d.'/JoinedPersistentCollection.class.php';
require_once $d.'/PersistentObject.class.php';
require_once $d.'/Validation/PWBException.class.php';
require_once $d.'/Validation/Validation.class.php';
require_once $d.'/Validation/ValidationException.class.php';
require_once $d.'/Validation/OneOfException.class.php';
require_once $d.'/Validation/EmptyFieldException.class.php';
require_once $d.'/Validation/LikeValidation.class.php';
require_once $d.'/Validation/LikeValidationException.class.php';
require_once $d.'/Validation/OrValidation.class.php';
require_once $d.'/Validation/PluggableValidation.class.php';


?>