<?php
class FilePersistenceManager extends PersistenceManager{
	function fileNameForObject(&$object){
		return $this->fileNameFor(getClass($object),$object->getID());
	}
	function fileNameFor($class, $id){
		return persistencydir.'/'.$class.'-'.$id.$this->extension();
	}


}

?>
