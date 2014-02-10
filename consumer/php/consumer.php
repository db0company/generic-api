<?php

$types = array('GET', 'POST', 'PUT', 'DELETE');
$formats = array('json', 'bool', 'string');

function consume($base_url, $resource, $format = 'json',
		 $type = 'GET', $id = null,
		 $get_parameters = array()) {
  $parameters = '?';
  foreach ($get_parameters as $key => $value)
    $parameters .= $key . '=' . $value . '&';

  $url = $base_url . '/' . $resource
    . ($id == null ? '' : '/' . $id)
    . $parameters . 'type=' . $type;

  if (!($result = @file_get_contents($url)))
    return false;

  if ($format == 'json')
    return json_decode($result, true);
  elseif ($format == 'bool') {
    if ($result == 'false')
      return false;
    return $result ? true : false;
  }
  return $result;
}

