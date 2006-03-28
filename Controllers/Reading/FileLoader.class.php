<?php

require_once dirname(__FILE__) . '/PersistentObjectLoader.class.php';

class FileLoader extends PersistentObjectLoader
{

  function populate($form, &$error_msgs) {

     $class = strtolower($this->obj->table);
      if (!is_uploaded_file($_FILES[$class.'filename']['tmp_name'])) {
          $error_msgs['filename'] = 'Could not upload the file '.$class.'filename';
          trigger_error ('File uploading unsuccessful');
          return false;
      }


    $fields =& $this->obj->allFields();

      $file_data = $_FILES[$class.'filename'];

      $bin_data = addslashes(fread(fopen($file_data["tmp_name"], "r"), $file_data["size"]));
      unlink($file_data["tmp_name"]);
      $bin_data_field =& $fields['bin_data'];
      $bin_data_field->setValue($bin_data);
      //unset($fields['bin_data']);

      $filesize_field =& $fields['filename'];
      $filesize_field->setValue($file_data['name']);
      //unset($fields['filename']);

      $filesize_field =& $fields['filesize'];
      $filesize_field->setValue($file_data['size']);
      //unset($fields['filesize']);

      $filetype_field =& $fields['filetype'];
      $filetype_field->setValue($file_data['type']);
      //unset($fields['filetype']);

      foreach($fields as $name=>$f){
        if (!$this->populateField($fields[$name], $form, $error_msgs)) return false;
      }

      if (!$this->obj->validate(&$error_msgs)) return false;

	  return true;
    }

    function updatePopulation($form, &$error_msgs) {


      $fields =& $this->obj->getFields(array('label','description'));

      foreach($fields as $name=>$f){
        if (!$this->populateField($fields[$name], $form, $error_msgs)) return false;
      }

      if (!$this->obj->validate(&$error_msgs)) return false;

      return true;
    }


    function doUpdate ($form, &$error_msgs) {
        $error_msgs = array(); // Array of strings
        $this->obj->setID($form["ObjID"]);
        $this->obj->load();
        $success = $this->updatePopulation($form, &$error_msgs);
        if ($success) {
            $this->obj->existsObject = TRUE;
            $this->obj->save();
            $this->obj->load();
            $this->linkColec($form);
            if (isset($form["newAddress"])) { $this->newaddress = $form["newAddress"]; };
            return TRUE;
        } else { // The population failed
            return false;
        }
    }
}

?>
