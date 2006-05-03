<?php



class XMLParser {
   var $parser;
   var $xmls = array();
   function &parse($data)
   {
       $this->parser = xml_parser_create('ISO-8859-1');

       xml_set_object($this->parser, $this);
       xml_set_element_handler($this->parser, 'tag_open', 'tag_close');
       xml_set_character_data_handler($this->parser, 'cdata');
       xml_parser_set_option($this->parser,XML_OPTION_CASE_FOLDING,FALSE);
       $arr = array();
       $this->xmls=&$arr;
       xml_parse($this->parser, $data);
       $x =& $this->xmls[0];
       return $x;
   }

   function tag_open($parser, $tag, $attributes)
   {
   	   $x =& new XMLNode;
   	   $x->setTagName($tag);
   	   foreach($attributes as $n => $v){
   	   	   $x->attributes[$n] = $v;
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