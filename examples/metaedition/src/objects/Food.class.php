<?php

class Food extends PersistentObject {

    function Food() {
        $this->table = "foods";
 /*       $this->sFormName = "Food";
        $this->permiso = 5;*/
        $this->addField(new IdField('id',false));
        $this->addField(new TextField('food', false));
    }
}
?>