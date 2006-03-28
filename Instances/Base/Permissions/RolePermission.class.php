<?
class RolePermission extends PersistentObject {
    function initialize () {
         $this->table = "RolePermission";
         $this->addField(new indexField("role", TRUE, Role));            
         $this->addField(new textField("permission", TRUE));
    }
}
?>
