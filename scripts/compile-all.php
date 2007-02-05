<?php
$app_dir = $_REQUEST['app_dir'];
$verbose = $_REQUEST['verbose'];
$_REQUEST['recompile'] = 'yes';

$output = '';

function handle_output($out) {
  global $output;
  $output = $out;
  return '';
}

echo 'Compiling proyect...';

ob_start('handle_output');


require_once $app_dir . 'Configuration/pwbapp.php';
Application::launch();

ob_end_flush();

echo "done\n";

if ($verbose) {
  echo "Output:\n";
  echo $output;
}


?>