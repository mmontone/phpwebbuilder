<?php

class Person extends PersistentObject
{
    function initialize() {
        $this->table = "users";
/*        $this->sFormName = "People";
        $this->permiso = 5;*/
        $this->addField(new TextField('user',false));
        $this->addField(new TextField('pass', false));
        //$this->addField(new CollectionField('emails', 'Email'));
        //$this->addField(new IndexField('favourite_food', true, 'Food'));
    }
}

?>