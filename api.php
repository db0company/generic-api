<?php
//
// Made by        db0
// Contact        db0company@gmail.com
// Website        http://db0.fr/
// Repo           https://github.com/db0company/invite
//

include_once('conf.php');

function typeChecker($type, $value) {
  if ($type == 'string')
    return (htmlspecialchars(stripslashes($value)));
  elseif ($type == 'int')
    return intval($value);
  elseif ($type == 'bool') {
    return $value ? true : false;
  }
  elseif ($type == 'password')
    return $value;
  return $value;
}

function checkParams($params, $required_params, $optional_params) {
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

function modelCall($function, $params) {
  include_once('model.php');
  global $conf;
  $model = new InviteModel($conf['sql']['login'],
                           $conf['sql']['pass'],
                           $conf['sql']['dbname'],
                           $conf['sql']['host'],
                           $conf['verbose']
                           );
  if (!method_exists($model, $function))
    return array(501);
  return $model->$function($params);
}

function main() {
  $type = isset($_GET['type']) ? $_GET['type'] : $_SERVER['REQUEST_METHOD'];
  $resource = $_GET['resource'];
  $id = isset($_GET['id']) ? $_GET['id'] : null;

  include_once('methods.php');
  foreach ($methods as $method) {
    if ($method['type'] == $type
        && $method['resource'] == $resource
        && (($method['one'] === true && $id)
            || $method['one'] === false && $id === null)
        ) {
      if (!($params = checkParams($_GET, $method['required_params'],
                                  $method['optional_params']))) {
        header("HTTP/1.1 400 Bad Request");
        echo 'Required parameters missing: ';
        echo implode(', ', array_keys($method['required_params']));
        return ;
      }
      if ($method['one'])
        $params['id'] = typeChecker('string', $id);
      $r = modelCall($method['function'], $params);
      if ($r === false) echo 'false';
      elseif ($r === true) echo 'true';
      elseif (is_array($r)) { // code, content
        if ($r[0] == 403) {
          header("HTTP/1.1 403 Forbidden");
          echo 'Authentication failed.';
        }
        elseif ($r[0] == 500) {
          header("HTTP/1.1 500 Internal Server Error");
          echo 'Something went wrong.';
        }
        elseif ($r[0] == 202) {
          header("HTTP/1.1 202 No Result");
        }
        elseif ($r[0] == 501) {
          header("HTTP/1.1 501 Not implemented");
          echo 'This method is not implemented. It should be!';
        }
        else {
          header("HTTP/1.1 ".$r[0]);
          echo $r[1];
        }
      }
      else print_r($r);
      return ;
    }
  }
  header("HTTP/1.1 404 Not found");
  echo 'No such method.';
  global $conf;
  if ($conf['verbose']) {
    echo ' type = '.$type.', resource = '.$resource.', id = '.$id;
  }
}

main();
