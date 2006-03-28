<?php

/*
class PersonDisplayer extends ObjectDisplayer
{

    function embedCollectionField(&$field) {
    	if ($field->name == 'telephones') {
    		$tel_displayer =& $this->telephonesDisplayerFor($field->collection);
            $this->addChildren($tel_displayer);
    	}
        else {
        	super->embedCollectionField($field);
        }
    }
    */
    /*
    function &telephonesDisplayerFor(&$collection) {
        $this->config->telephonesDisplayerFor($collection);
    }

}
  */

/*
$person_config->addChildrenConfiguration('telephones', new PrettyCollectionConfiguration());
$app_config =& new ComponentConfiguration();
$app_config->addClassConfiguration('Person', $person_config);
$project_config =& new ComponentConfiguration();
$project_config->addChildrenConfiguration('person_in_charge', $person_config);
//$app_config->addClassConfiguration('Project', $project_config);
$app_config->addChildrenConfiguration('Project', $project_config);
$app_config->getDisplayerFor(&$object);
$app_config->getEditorFor(&$object);
*/

/*

YAML like


*/

?>