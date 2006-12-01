<?php

$d = dirname(__FILE__);

compile_once ($d.'/PWBObject.class.php');
compile_once ($d.'/Collection.class.php');
compile_once ($d.'/FunctionObject.class.php');
compile_once ($d.'/WeakReference.class.php');
compile_once ($d.'/PWBFactory.class.php');
compile_once ($d.'/PWBException.class.php');
compile_once ($d.'/ValueModels/ValueModel.class.php');
compile_once ($d.'/ValueModels/ValueHolder.class.php');
compile_once ($d.'/ValueModels/ObjectHolder.class.php');
compile_once ($d.'/ValueModels/AspectAdaptor.class.php');
compile_once ($d.'/ValueModels/PluggableAdaptor.class.php');
compile_once ($d.'/ValueObjects/ValueObject.class.php');
compile_once ($d.'/ValueObjects/Null.class.php');
compile_once ($d.'/ValueObjects/Number.class.php');
compile_once ($d.'/ValueObjects/String.class.php');
compile_once ($d.'/ValueObjects/Vector.class.php');
compile_once ($d.'/conditions/PWBCondition.class.php');

?>
