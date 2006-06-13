<?

class File extends PersistentObject {
      function initialize () {
               $this->table = "files";
               $this->addField(new TextField('label', true));
               $this->addField(new FilenameField("filename", true));
               $this->addField(new NumField("filesize", false));
               $this->addField(new textField("filetype", true));
               $this->addField(new BlobField("bin_data", FALSE));
               $this->addField(new TextArea("description", FALSE));
       }

       function &visit(&$visitor) {
            return $visitor->visitedFile($this);
       }

       function validate(&$error_msgs) {
       	    return $this->checkNotEmpty(array('label'), $error_msgs);
       }
       function downloadLink(){
       		return pwb_url."/lib/ReadFile.php?id=".$this->id->getValue();
	   }
}

?>
