<?php
//
// Made by        db0
// Contact        db0company@gmail.com
// Website        http://db0.fr/
// Repo           https://github.com/db0company/generic-api
//

include_once('conf.php');
include_once("include.php");

///////////////////////////////////////////////////////////
// Checkers
///////////////////////////////////////////////////////////

function typeChecker($type, $value) {
  if ($type == 'string')
    return (htmlspecialchars(stripslashes($value)));
  elseif ($type == 'int')
    return intval($value);
  elseif ($type == 'bool') {
    return $value ? true : false;
  }
  elseif ($type == 'list')
    return implode(',', $value);
  elseif ($type == 'password')
    return $value;
  return $value;
}

function paramsChecker($params, $required_params, $optional_params) {
  $finalParams = array();
  // check required
  foreach ($required_params as $key => $type) {
    if (!array_key_exists($key, $params))
      return false;
    $finalParams[$key] = typeChecker($type, $params[$key]);
  }
  // check optional
  foreach ($optional_params as $key => $default_value) {
    $finalParams[$key] =
      array_key_exists($key, $params) ?
      typeChecker(gettype($default_value), $params[$key])
      : $default_value;
  }
  return $finalParams;
}

///////////////////////////////////////////////////////////
// Errors handling
///////////////////////////////////////////////////////////

function errorHandler_aux($error_code, $method) {
  global $errors;
  $error['code'] = $error_code;
  $error['text'] = $errors[$error['code']][0];
  $error['content'] = is_callable($errors[$error['code']][1]) ?
    $errors[$error['code']][1]($method) : $errors[$error['code']][1];
  return $error;
}

function errorHandler($error, $method = null) {
  global $errors, $default_error;
  if (is_array($error));
  elseif ((is_int($error)
	   || intval($error) != 0)
	  && array_key_exists($error, $errors))
    $error = errorHandler_aux($error, $method);
  else
    $error = errorHandler_aux($default_error, $method);
  header('HTTP/1.1 '.$error['code'].' '.$error['text']);
  echo $error['content'];
}

///////////////////////////////////////////////////////////
// API Call method
///////////////////////////////////////////////////////////

function api($methods) {
  header("Access-Control-Allow-Origin: *");
  $allparams = array_merge($_GET, $_POST);
  $type = isset($allparams['type']) ? $allparams['type'] : $_SERVER['REQUEST_METHOD'];
  $resource = $allparams['resource'];
  $id = isset($allparams['id']) ? $allparams['id'] : null;

  if ($type == 'OPTIONS') {
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    return ;
  }

  foreach ($methods as $method) {
    if ($method['type'] == $type
        && $method['resource'] == $resource
        && (($method['one'] === true && $id)
            || $method['one'] === false && $id === null)
        ) {
      if (($params = paramsChecker($allparams, $method['required_params'],
				   $method['optional_params'])) === false)
	return errorHandler(400, $method);
      if ($method['one'])
        $params['id'] = typeChecker('string', $id);
      if (!is_callable($method['function']))
	return errorHandler(501, $method);
      try { $r = $method['function']($method['resource'], $id, $params); }
      catch (Exception $code) { return errorHandler($code->getMessage(), $method); }
      echo convertResponse($r, isset($allparams['format']) ? $allparams['format'] : null);
      return ;
    }
  }
  return errorHandler(404);
}

include_once('methods.php');
api($methods);
