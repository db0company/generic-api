<?php
//
// Made by        db0
// Contact        db0company@gmail.com
// Website        http://db0.fr/
// Repo           https://github.com/db0company/generic-api
//

include_once("mysql.php");

///////////////////////////////////////////////////////////
// Set Default values on methods
///////////////////////////////////////////////////////////

function setDefault($methods) {
  foreach ($methods as $i => $method) {
    if (!isset($method['type']))
      $methods[$i]['type'] = 'GET';
    if (!isset($method['one']))
      $methods[$i]['one'] = false;
    if (!isset($method['function']))
      $methods[$i]['function'] = mySQL_function($method['type'], $method['one']);
    if (!isset($method['auth_required']))
      $methods[$i]['auth_required'] = false;
    if (!isset($method['optional_params']))
      $methods[$i]['optional_params'] = array();
    if (!isset($method['required_params']))
      $methods[$i]['required_params'] = array();
    if (!isset($method['response']))
      $methods[$i]['response'] = 'string';
    if (!isset($method['doc']))
      $methods[$i]['doc'] = mySQL_doc($method['resource'], $method['type'], $method['one']);
  }
  return $methods;
}

///////////////////////////////////////////////////////////
// Convert Response according to the format
///////////////////////////////////////////////////////////

function convertResponse($response, $format = null) {
  global $conf;
  if ($response === false) return 'false';
  elseif ($response === true) return 'true';
  elseif (is_int($response)) return (string)$response;
  elseif (is_string($response)) return $response;
  if (!$format)
    $format = $conf['format'];
  if ($format == 'json' && is_array($response))
    return json_encode(array_reverse($response));
  if ($format == 'php')
    return print_r($response, true);
  return $response;
}

///////////////////////////////////////////////////////////
// Errors
///////////////////////////////////////////////////////////

$default_error = 500;
$errors = array(
		// code => a(text, text content or function, is global error)
		202 => array('No Result', '', false),
		400 => array('Bad Request', function($method) {
		    return !$method ? 'Bad Request'
		      : ('Required parameters missing: '
			 .implode(', ', array_keys($method['required_params'])));
		  }, false),
		403 => array('Forbidden', 'Authentication failed.', false),
		404 => array('Not Found', function($method) {
		    return !$method ? 'No such method' : 'Not Found';
		  }, true),
		500 => array('Internal Server Error', 'Something went wrong.', true),
		501 => array('Not implemented', 'This method is not implemented. It should be!', true),
		);
