<?php
require_once dirname(__FILE__).'/../../Configuration/pwbapp.php';
require_once dirname(__FILE__).'/../pwb.php';

$obj =& File::getWithId('File', $_REQUEST["id"]);
header('Content-Type: '.$obj->filetype->getValue());
header('Content-Disposition: attachment; filename="'.$obj->filename->getValue().'"');
print $obj->bin_data->getValue();
?>