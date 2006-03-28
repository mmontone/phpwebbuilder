<?php
/*
 * Created on 11-feb-2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

require_once dirname(__FILE__) . '/ConfigReader.class.php';
$config_reader =& new ConfigReader();
$config_reader->read(dirname(__FILE__) . '/config.ini');
require_once pwbdir . '/newpwb.php';

includefile('src');

?>
