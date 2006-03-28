<?php

class EditPerson extends Application
{
    function &set_root_component() {
        $person =& new Person();
        $person->setID(1);
        $person->load();
        $config =& $this->configure();
        $displayer =& $config->displayerFor($person);
		return $displayer;
    }

    function &configure() {
    	$config =& new ComponentConfiguration();
        return $config;
    }
}
?>