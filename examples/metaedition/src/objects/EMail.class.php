<?php

class EMail extends PersistentObject {
    function EMail() {
        $this->table = "emails";
/*        $this->sFormName = "People";
        $this->permiso = 5;*/
        $this->addField(new IdField('id', false));
        $this->addField(new IndexField('id_person',false,'Person'));
        $this->addField(new EmailField('email', false));
    }
}

?>