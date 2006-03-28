<?php

class Request
{
  var $url;
  var $params;
  function Request() {
    $url = '';
    $params = array();
  }

  function href() {
    $href = $this->url . '?';
    foreach($this->params as $key => $value) {
      $href .= "&amp;$key=$value";
    }
    return $href;
  }

  function set($key, $value) {
    $this->params[$key] = $value;
  }

  function get($key) {
    return $this->params[$key];
  }
}