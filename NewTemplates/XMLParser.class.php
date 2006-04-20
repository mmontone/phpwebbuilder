<?php

class XMLParser {
   var $parser;
   var $xmls = array();
   function XMLParser()
   {
       $this->parser = xml_parser_create('ISO-8859-1');

       xml_set_object($this->parser, $this);
       xml_set_element_handler($this->parser, "tag_open", "tag_close");
       xml_set_character_data_handler($this->parser, "cdata");
   }

   function &parse($data)
   {
       xml_parse($this->parser, $data);
       $x =& $this->xmls[0];
       $this->xmls=array();
       return $x;
   }

   function tag_open($parser, $tag, $attributes)
   {
   	   $x =& new XMLNode;
   	   $x->setTagName(strtolower($tag));
   	   foreach($attributes as $n => $v){
   	   	   $x->attributes[strtolower($n)] = $v;
   	   }
   	   $this->xmls[]=& $x;
   }

   function cdata($parser, $cdata)
   {
   	   if (trim($cdata)!=""){
   	   		$cant = count($this->xmls); 	
	   		$n = null;
       		$this->xmls[$cant-1]->append_child(new XMLTextNode($cdata, $n));
   	   }
   }

   function tag_close($parser, $tag)
   {
   	    $cant = count($this->xmls);
       	if ($cant>=2){
	   		$xchild =& $this->xmls[$cant-1];
	       	array_pop($this->xmls);
	   		$this->xmls[$cant-2]->append_child($xchild);
       	}
   }
}

?>