<?

class UserRole extends PersistentObject {
    function initialize () {
         $this->table = "UserRole";
         $this->addField(new indexField("user", TRUE, User));            
         $this->addField(new indexField("role", TRUE, Role));
    }
}
?>
