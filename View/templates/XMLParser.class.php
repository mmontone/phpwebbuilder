<?php



class XMLParser {
   var $parser;
   var $xmls;
   var $text;
   function &parse($data,$f)
   {
       $parser = xml_parser_create('ISO-8859-1');

       xml_set_object($parser, $this);
       xml_set_element_handler($parser, 'tag_open', 'tag_close');
       xml_set_character_data_handler($parser, 'cdata');
       xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,FALSE);
       $arr = array();
       $this->xmls=&$arr;
       $err = xml_parse($parser, $data);
       $x =& $this->xmls[0];
 	   if (!$x->childNodes) {
          die(sprintf("XML error: %s at line %d, column %d, %s, %s",
                   xml_error_string(xml_get_error_code($parser)),
                   xml_get_current_line_number($parser),
                   xml_get_current_column_number ($parser)+1,$f, print_r($x,TRUE)));
   	   }
       xml_parser_free($parser);
       return $x;
   }

   function tag_open($parser, $tag, $attributes)
   {
  	   $x =& new XMLNode;
   	   $x->setTagName($tag);
   	   $n=null;
   	   $this->text =& $n;
   	   $x->attributes =& $attributes;
   	   $this->xmls[]=& $x;
   }

   function cdata($parser, $cdata)
   {
   		if ($this->text==null && trim($cdata)!=''){
   			$this->text =& new XMLTextNode($cdata);
   			$this->xmls[count($this->xmls)-1]->appendChild($this->text);
   		} else {
   			$this->text->data.=$cdata;
   		}
   }
   function tag_close($parser, $tag)
   {
   	   	$xs =& $this->xmls;
   	    $cant = count($xs);
       	if ($cant>=2){
	   		$xchild =& $xs[$cant-1];
	   		$n=null;
   	   		$this->text =& $n;
	       	array_pop($xs);
	   		$xs[$cant-2]->appendChild($xchild);
       	}
   }
}
?>