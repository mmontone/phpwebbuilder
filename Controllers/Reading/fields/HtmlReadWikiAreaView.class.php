<?php

require_once("HtmlReadFieldView.class.php");

/*

Examples:

[[Link:Indumentaria/Remera10|Nuestra nueva remera]]
[[Imagen:Logo93 {width:300px}|de ese año]]

In general:
[[Type:Path [{css style}] | text]]

*/

//const var<-- eliminado porque no funciona en PHP4 
$wiki_commands = array('Image','Link');

class HtmlReadWikiAreaView extends HtmlReadFieldView
{

    function formObject ($object) {
		$ret .= "\n                     <textarea name=\"";
		$ret .= $this->frmName($object);
		$ret .= "\" >";
		$ret .= $this->field->convFromHTML($this->textFormatted());
		$ret .= "\n                     </textarea>";
		return $ret;
	}

    function textFormatted() {
        return preg_replace_callback('/[[([alpha])+]]/', array($this, "command_handler"), $this->field->value());
    }

    function command_handler($command_str) {
        $command_name = $this->getCommandName($command_str);
        if (!(array_find($command_name, $wiki_commands))) {
            return "Unknown command: " . $command_name;
        }
        $wiki_command_class = $command_name . "Command";
        $wiki_command = new $wiki_command_class($command_str);
        return $wiki_command->show();
    }
}

class WikiCommand
{
    var $command_str;
    var $name;
    var $path;
    var $style;
    var $text;


    function WikiCommand($command_str) {
        $this->command_str = $command_str;
    }

    function parse() {
        $id = '(?:[[:alpha:]]|[[:digit:]])+';
        $command_name = $id;
        $command_path = $id . '(?:\/' . $id . ')*';
        $style = '(?:.)*';
        $text = '(?:[[:alpha:]]|[[:digit:]]|\s)+';
        $command_sintax = '/^\[\[(' . $command_name . ')\:(' . $command_path . ')\s*(?:\{(' . $style . ')\})?\s*\|('. $text .')\]\]$/';
        $matches = array();
        if (!($ret = preg_match($command_sintax, $this->command_str, $matches)))
            return false;
        $this->name = $matches[1];
        $this->path = $matches[2];
        $this->style = $matches[3];
        $this->text = $matches[4];
        return true;

    }

    function show() {
        if (!$this->parse()) return $this->command_str;
        return $this->render();
    }

    function render() {
        $ret = '<a href=' . $this->resource_link();
        if (!empty($this->style))
            $ret .= ' style="'. $this->style . '"';
        $ret .= '>' . $this->text . '</a>';
        return $ret;
    }
}

class ImageWikiCommand extends WikiCommand
{
     function render() {
        $ret = '<img href=' . $this->resource_link();
        if (!empty($this->style))
            $ret .= ' style="'. $this->style . '"';
        $ret .= '>' . $this->text . '</a>';
        return $ret;
     }

    function resource_link() {
        return $this->site_root_link() . '/images/' . $this->path;
    }
}

class LinkWikiCommand extends WikiCommand {}


?>
