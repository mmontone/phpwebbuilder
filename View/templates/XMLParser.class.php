<?php



class XMLParser {
   var $parser;
   var $xmls;
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
          die(sprintf("XML error: %s at line %d, column %d, %s",
                   xml_error_string(xml_get_error_code($parser)),
                   xml_get_current_line_number($parser),
                   xml_get_current_column_number ($parser)+1,$f));
   	   }
       xml_parser_free($parser);
       return $x;
   }

   function tag_open($parser, $tag, $attributes)
   {
   	   $x =& new XMLNode;
   	   $x->setTagName($tag);
   	   $atts =& $x->attributes;
   	   foreach($attributes as $n => $v){
   	   	   $atts[$n] = $v;
   	   }
   	   $this->xmls[]=& $x;
   }

   function cdata($parser, $cdata)
   {
   	   	$xs =& $this->xmls;
   	   	$cant = count($xs);
   		$xchild =& $xs[$cant-1]->last_child();
   		if (getClass($xchild)=='xmltextnode'){
			$xchild->data.=$cdata;
   		} else {
   			if (trim($cdata)!=""){
	       		$xs[$cant-1]->appendChild(new XMLTextNode($cdata));
   			}
   		}
   }
   function tag_close($parser, $tag)
   {
   	   	$xs =& $this->xmls;
   	    $cant = count($xs);
       	if ($cant>=2){
	   		$xchild =& $xs[$cant-1];
	       	array_pop($xs);
	   		$xs[$cant-2]->appendChild($xchild);
       	}
   }
}
?>