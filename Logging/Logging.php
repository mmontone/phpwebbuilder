<?php
/*
 * Created on 07-feb-2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 *
 * Use:
 *
 * Access logging.php through the browser. Access out.log (i.e. through the browser)
 */

if (defined('logging')){
 require_once peardir.'Log.php';
if (logging=='window'){
	$conf = array('title' => 'Site logging');
	$GLOBALS['logger'] = &Log::singleton('win', 'LogWindow', 'ident', $conf);
} else {
	$GLOBALS['logger'] = & Log::singleton('file', logging);
}
$GLOBALS['logger']->log('lala');
function errorHandler($code, $message, $file, $line)
{
    global $logger;

    /* Map the PHP error to a Log priority. */
    switch ($code) {
    case E_WARNING:
    case E_USER_WARNING:
        $priority = PEAR_LOG_WARNING;
        break;
    case E_NOTICE:
    case E_USER_NOTICE:
        $priority = PEAR_LOG_NOTICE;
        break;
    case E_ERROR:
    case E_USER_ERROR:
        $priority = PEAR_LOG_ERR;
        break;
    default:
        $priotity = PEAR_LOG_INFO;
    }
    $GLOBALS['logger']->log($message . ' in ' . $file . ' at line ' . $line,
                 $priority);

}

//set_error_handler('errorHandler');

/* Use always the trigger_error function to trigger any kind of errors or notifications!! */
trigger_error('************** Beggining log ************', E_USER_NOTICE);

}
?>