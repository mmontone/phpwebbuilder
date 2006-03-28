<?php
require_once dirname(__FILE__) . '/../Controller.class.php';

class ReadImage extends Controller
{
  function start ($form) {
    // Create an empty object of the type specified in the form
    $obj =& File::getWithId('File', $form["id"]);
    //Change content type to your file type
    header("Content-Type: $load->obj->filetype->value");
    print $obj->bin_data->value;
  }
}

?>