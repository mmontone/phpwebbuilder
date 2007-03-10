<?

class Role extends PersistentObject {
    function initialize () {
         $this->table = "Role";
         $this->addField(new textField("name", TRUE));
         $this->addField(new textArea("description", FALSE));
         $this->addField(new CollectionField('role',array('type'=>'RolePermission','display'=>'Permissions')));
    }
}
?>
