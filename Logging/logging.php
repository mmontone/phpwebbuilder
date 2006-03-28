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

 require_once peardir.'Log.php';

//$console = &Log::factory('console', '', 'TEST');
//$console->log('Logging to the console.');


/*
This logger can be used when the application is to be deployed.
A null handler may be adecuate if we don't want any logging
*/
/*
$logger = &Log::singleton('file', 'site.log');
$logger->log('Logging to site.log.');
*/


/*
 When developing, this handler is very adecuate. Other options are the file handler and the console handler

*/

$conf = array('title' => 'Site logging');
$logger = &Log::singleton('win', 'LogWindow', 'ident', $conf);


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

    $logger->log($message . ' in ' . $file . ' at line ' . $line,
                 $priority);
}

set_error_handler('errorHandler');

/* Use always the trigger_error function to trigger any kind of errors or notifications!! */
trigger_error('************** Beggining log ************', E_USER_NOTICE);

/* Testing


for ($i = 1; $i < 100; $i++) {
    trigger_error('This is an information log message.', E_USER_NOTICE);
}

*/

/*
function print_backtrace($error) {
  echo backtrace_string($error);
}

function backtrace_string($error) {
    $back_trace = debug_backtrace();
    $ret = "<h1>$error</h1>";
    foreach ($back_trace as $trace) {
        $ret .= "<b> {$trace['file']}: {$trace['line']} ({$trace['function']})</b></br>";
    }
    return $ret;
}
*/
/***************/
/* Assertions  */
/***************/

// Active assert and make it quiet
assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_WARNING, 0);
assert_options(ASSERT_QUIET_EVAL, 1);

// Create a handler function
function my_assert_handler($file, $line, $code)
{
   echo "<hr>Assertion Failed:
       <b>File</b> '$file'<br />
       <b>Line</b> '$line'<br />
       <b>Code</b> '$code'<br />";
   print_backtrace();
   echo  "</hr>";
}

// Set up the callback
assert_options(ASSERT_CALLBACK, 'my_assert_handler');

?>
