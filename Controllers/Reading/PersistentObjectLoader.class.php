<?php
/**
 * Loads an PersistentObject. Has methods for each possible Action.
 *
 */
/*
TODO: there shouldn't be a loader. Each object should know how to populate himself.
Besides, doInsert and others belong to the controller.
Proof:

if (isset($form["newAddress"])) { $this->newaddress = $form["newAddress"]; };

in doInsert

*/

require_once dirname(__FILE__) . '/Loader.class.php';

class PersistentObjectLoader extends Loader  {

    function readForm ($form, &$error_msgs) {
    	$func = "do".$form["Action"];
    	trace ($form["Action"]."ing...");
    	return $this->$func($form, $error_msgs);
    }
    function populate ($form, &$error_msgs) {
       $success = true;
       $error_msgs = array();
       foreach ($this->obj->allFieldNames() as $index) {
            // Populate the object "allFields()"
            $field =& $this->obj->$index;
            $success = $success && $this->populateField($field, $form, $error_msgs);
        }
       // Check object
       $success = $success && $this->obj->validate(&$error_msgs);
       if (!$success) trace(print_r($error_msgs, TRUE));
       return $success;
    }

    function populateField(&$field, &$form, &$error_msgs) {
      $html =& $this->fieldShowObject($field);
      // Checks the field data
      if (!$html->readForm($this->obj, $form)) {
        $error_msgs [$field->colName] = "The " . $field->colName . " is invalid";
        return false;
      }
      return true;
    }

    function doInsert ($form, &$error_msgs) {
        $error_msgs = array(); // Array of strings
        $success = $this->populate($form, &$error_msgs);
        if ($success) {
            $this->obj->save();
            $this->obj->load();
            $this->linkColec($form);
	    } else { // The population failed
            return false;
        }
        if (isset($form["newAddress"])) { $this->newaddress = $form["newAddress"]; };
        return TRUE;
    }
    function doUpdate ($form, &$error_msgs) {
        $error_msgs = array(); // Array of strings
        $this->obj->setID($form["ObjID"]);
        $success = $this->populate($form, &$error_msgs);
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
    function doShow($form, &$error_msgs) {
      $this->doList($form, $error_msgs);
    }

    function doEdit ($form, &$error_msgs) {
        $error_msgs = array(); // Array of strings
        $this->obj->setID($form["ObjID"]);
        $this->obj->load();
        if (isset($form["newAddress"])) { $this->newaddress = $form["newAddress"]; };
        return TRUE;
    }
    function doList ($form, &$error_msgs) {
        if (isset($form["Delete" . $this->obj->getId()])){
          $this->obj->delete();
          $this->linkColec($form);
        }
    }
    function doAdd ($form, &$error_msgs) {
        $error_msgs = array(); // Array of strings
		$this->populate($form, &$error_msgs);
        if (isset($form["newAddress"])) { $this->newaddress = $form["newAddress"]; };
        return TRUE;
    }
    function doDelete ($form, &$error_msgs) {
        $error_msgs = array(); // Array of strings
        if ($form["ObjID"]) { $this->obj->setID($form["ObjID"]); };
        $this->obj->load();
        if (isset($form["Delete" . $this->obj->getId()])){
          $this->obj->delete();
          $this->linkColec($form);
        }
        if (isset($form["newAddress"])) { $this->newaddress = $form["newAddress"]; };
        return TRUE;
    }
    function formName() {
        return $this->obj->tableName() . $this->obj->getID();
    }
    function linkColec($form){
		$sc = new ShowController;
		/*TODO:
		 * Eliminate the lack of newaction.
		 */
		trace("Now showing the collection");
		$form["Action"] = "List";
		return $sc->execute("start", $form);
    }
}

?>
