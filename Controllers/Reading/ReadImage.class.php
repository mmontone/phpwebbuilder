<?php
require_once dirname(__FILE__) . '/../Controller.class.php';

class ReadFile extends Controller
{
  function start ($form) {
    $obj =& File::getWithId('File', $form["id"]);
    header('Content-Type: '.$load->obj->filetype->value);
    print $obj->bin_data->value;
  }
}

class ReadImage extends ReadFile{}

?>