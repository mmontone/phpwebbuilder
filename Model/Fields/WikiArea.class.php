<?php

require_once dirname(__FILE__) . '/DataField.class.php';

class WikiArea extends TextArea
{
      function WikiArea ($name, $isIndex) {
               parent::TextArea($name, $isIndex);
      }

      function &visit(&$obj) {
               return $obj->visitedWikiArea($this);
      }

      function asHTML() {
        $html = parent::asHTML();
        $html = preg_replace_callback('/<a href="((?:.)*)"/', array($this, 'translateHRef'), $html);
        $html = preg_replace_callback('/<img src="((?:.)*)"/', array($this, 'translateImgSrc'), $html);
        return $html;
      }

      function translateHRef($matches) {
        $href = $matches[1];
        $id = '(?:[[:alpha:]]|[[:digit:]])+';
        $obj_type_sintax = $id;
        $obj_id_sintax = $id;
        $command_path = '(' . $obj_type_sintax . ')\/(' . $obj_id_sintax . ')';
        if (preg_match('/^' . $command_path . '$/', $href,  $matches)) {
          $obj_type = $matches[1];
          $obj_id = $matches[2];
          $new_href = site_url . "admin/Action.php?Controller=ShowController&ObjType=$obj_type&ObjID=$obj_id";
          return '<a href="' . $new_href . '"';
        }
        else {
          return $href;
        }
      }

      function translateImgSrc($matches) {
        $src = $matches[1];
        $id = '(?:[[:alpha:]]|[[:digit:]])+';
        $obj_id_sintax = $id;
        $command_path = $obj_id_sintax;
        if (preg_match('/^(' . $command_path . ')$/', $src,  $matches)) {
          $obj_id = $matches[1];
          $new_src = site_url . "admin/action.php?Controller=ReadImage&id=$obj_id";
          return '<img src="' . $new_src . '"';
        }
        else {
          return $src;
        }
      }
}
?>
