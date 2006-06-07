<?php
require_once dirname(__FILE__) . '/../Controller.class.php';

class ReadFile extends Controller
{
  function begin ($form) {
    $obj =& File::getWithId('File', $form["id"]);
    header('Content-Type: '.$obj->filetype->value);
    header('Content-Disposition: attachment; filename='.$obj->filename->value);
    print $obj->bin_data->value;
  }
}

class ReadImage extends ReadFile{}

?>