<?

xdebug_start_profiling();
define('pwbapp','DefaultCMSApplication');
require_once '/var/www/alex/eurekacozzuol/src/Configuration/pwbapp.php';
$max=1;
Application::restart();
ob_start();
$t =& QKTest::getWithId('QKTest',51);
	new QuicKlickReprise($t);
ob_end_clean();
xdebug_dump_function_profile(4);


?>