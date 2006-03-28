<?php

/* legacy object */
class Person extends MetaObject
{
	/* These may not be necesary. They are added in runtime when needed. Getters and setters
	are added in runtime. See generation of get_brothers and list_brothers_in_range 
	var $name;
	var $surname;
	var $dni;
	var $email;
	var $birth_date;
	var $brothers; */
	
	function meta_description() {
		return array('dni'        => array('type' => 'text',
		                                   'validation' => 'is_valid_dni'),
					 'name'       => array('type' => 'text',
					                       'size' => 10),
		             'surname'    => 'text',
		             'email'      => 'email',
		             'birth_date' => 'date',
		             'phones'     => array('type' => 'list',
		                                   'elem_types' => 'string',
		                                   'validation' => 'is_valid_phone'),
		             'mother'     => array('type' => 'object',
		                                   'object_type' => 'Person',
		                                   'universe' => 'can_be_mother_of'),
		             'father'     => array('type' => 'object',
		                                   'object_type' => 'Person',
		                                   'universe' => 'can_be_father_of'),
		             'brothers'   => array('type'  => 'list',
		                                   'elem_types' => 'Person'));
	}
	
	/* A MetaObject is not persistent 
	function persistence_configuration() {
		return array('table_name'  => 'Persons', 
		             'table_index' => 'dni', 
		             'mapping' => array('name' => 'person_name')); 
	}      */
}

class Phone
{
	var $location;
	var $type;
	var $telephone;
	
	function Phone($telephone, $location='home', $type='normal') {
		$this->location = $location;
		$this->type = $type;
		$this->telephone = $telephone;
	}
}

class LegacyApplication extends Application 
{
	function &set_root_component() {
		$person =& $this->initialize_person();
		$component =& new MetaViewerComponent($person);
		$component->configure($this->configure_ui()); 
		$component->add_decoration(new ActionsDecorator(array('edit')));
		$component->add_decoration(new NavigationDecorator(array('back')));
	}
	
	function initialize_person() {
		/* don't have bd yet */
		$person =& new Person();
		/* Now we access the object throw the attribute aware generated methods*/
		$person->set_dni('30397839');
		$person->set_name('Mariano');
		$person->set_surname('Montone');
		$person->set_birthdate(new Date(28,6,1983));
		$person->set_email('mariano_montone@yahoo.com.ar');
		return $person;
	}
	
	function configure_ui() {
		/* It is really a pitty we don't have blocks */
		return array('friends' => array('style' => 'link_list',
		                                'action' => 'view_friend',
		                                'page_size' => 10),
		             'phones' => array('style' => 'embedded',
		                               'actions' => array('edit','delete')),
		                               'page_size' => 3);
	}
	
	function view_friend($friend) {
		$component =& MetaViewerComponent($friend);
		$component->add_decoration(new ActionsDecorator(array('edit')));
	}
}

?>