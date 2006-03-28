<?

class File extends PersistentObject {
      function initialize () {
               $this->table = "files";
               $this->addField(new TextField('label', true));
               $this->addField(new FilenameField("filename", false));
               $this->addField(new NumField("filesize", false));
               $this->addField(new textField("filetype", true));
               $this->addField(new BlobField("bin_data", FALSE));
               $this->addField(new TextArea("description", FALSE));
       }

       function &visit(&$visitor) {
            return $visitor->visitedFile($this);
       }

       function validate(&$error_msgs) {
       	    return $this->check_not_null(array('label'), $error_msgs);
       }
}

?>
