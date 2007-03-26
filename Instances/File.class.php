<?

class File extends PersistentObject {
      function initialize () {
               $this->table = "files";
               $this->addField(new TextField('label', FALSE));
               $this->addField(new FilenameField("filename", FALSE));
               $this->addField(new NumField("filesize", false));
               $this->addField(new textField("filetype", FALSE));
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
       		return site_url."Action.php?app=ReadFile&fileid=".$this->id->getValue();
	   }
}

?>
