<?php
//
// Made by        db0
// Contact        db0company@gmail.com
// Website        http://db0.fr/
// Repo           https://github.com/db0company/generic-api
//

include_once("conf.php");

function mySQL_db() {
  global $conf;
  if (!isset($conf['sql']['db'])) {
    $conf['sql']['db'] = new PDO('mysql:host='.$conf['sql']['host'].';'
				 .'dbname='.$conf['sql']['dbname'],
				 $conf['sql']['login'],
				 $conf['sql']['pass']);
    $conf['sql']['db']->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }
  if (!$conf['sql']['db'])
    throw new Exception(500);
  return $conf['sql']['db'];
}

function mySQL_execute($request, $params = array()) {
  try { return $request->execute($params); }
  catch (PDOException $e) {
    global $conf;
    if ($conf['verbose'] === true) {
      echo '<pre>';
      print_r($e);
      echo '</pre>'."\n";
    }
    return false;
  }
}

function mySQL_response($r, $one = true) {
  $func = function($r) {
    $f = array();
    foreach ($r as $k => $v)
    if (!is_int($k))
      $f[$k] = $v;
    return $f;
  };
  if ($one)
    return $func($r->fetch());
  $r = $r->fetchAll();
  $f = array();
  foreach ($r as $a)
    $f[] = $func($a);
  return $f;
}

function mySQL_GET($resource, $_, $_, $where = '', $where_params = array()) {
  $db = mySQL_db();
  $r = $db->prepare('SELECT * FROM '.$resource.' '.$where);
  if (!(mySQL_execute($r, $where_params)))
    throw new Exception(500);
  if (!($r->rowCount()))
    throw new Exception(202);
  return mySQL_response($r, false);
}

function mySQL_GET_one($resource, $one, $_, $where = '', $where_params = array()) {
  $db = mySQL_db();
  $r = $db->prepare('SELECT * FROM '.$resource.' WHERE id=? '.$where);
  if (!(mySQL_execute($r, array_merge(array($one), $where_params))))
    throw new Exception(500);
  if ($r->rowCount() === 0)
    throw new Exception(404);
  return mySQL_response($r, true);
}

function mySQL_POST($resource, $_, $params, $more = '') {
  $db = mySQL_db();
  $params = mySQL_filter_booleans($params);
  $q = 'INSERT INTO '.$resource.'(`'.implode('`, `', array_keys($params))
    .'`) VALUES(' .implode(', ', array_fill(0, count($params), '?')).')'.$more;
  $r = $db->prepare($q);
  if (!(mySQL_execute($r, array_values($params))))
    throw new Exception(500);
  return mySQL_GET_one($resource, $db->lastInsertId(), array());
}

function mySQL_filter_booleans($params) {
  foreach ($params as $i => $p) {
    if ($p == 'true')
      $params[$i] = true;
    elseif ($p == 'false')
      $params[$i] = false;
  }
  return $params;
}

function mySQL_PUT($resource, $one, $params, $filter = true) {
  $db = mySQL_db();
  if ($filter)
    $params = array_filter($params);
  $params = mySQL_filter_booleans($params);
  if (!empty($params)) {
    $q = 'UPDATE '.$resource.' SET `'.implode('`=?, `', array_keys($params)).'`=? WHERE id=?';
    $r = $db->prepare($q);
    if (!(mySQL_execute($r, array_merge(array_values($params), array($one)))))
      throw new Exception(500);
  }
  return mySQL_GET_one($resource, $one, null);
}

function mySQL_DELETE($resource, $_, $_, $where = '', $where_params = array()) {
  $db = mySQL_db();
  $r = $db->prepare('DELETE FROM '.$resource.' '.$where);
  if (!(mySQL_execute($r, $where_params)))
    throw new Exception(500);
  return "";
}

function mySQL_DELETE_one($resource, $one, $_, $where = '', $where_params = array()) {
  $db = mySQL_db();
  $r = $db->prepare('DELETE FROM '.$resource.' WHERE id=? '.$where);
  if (!(mySQL_execute($r, array_merge(array($one), $where_params))))
    throw new Exception(500);
  if (!($r->rowCount()))
    throw new Exception(404);
  return "";
}

function mySQL_invalid($resource, $one, $params) {
  throw new Exception(501);
}

$mySQL_which = array(
		     array('GET', false, mySQL_GET, 'Get all the $resources'),
		     array('GET', true, mySQL_GET_one, 'Get a $resource'),
		     array('POST', false, mySQL_POST, 'Create a new $resource'),
		     array('POST', true, mySQL_POST, 'Create a new $resource with this id'),
		     array('PUT', true, mySQL_PUT, 'Edit a $resource'),
		     array('DELETE', false, mySQL_DELETE, 'Delete all the $resources'),
		     array('DELETE', true, mySQL_DELETE_one, 'Delete a $resource'),
		     );

function mySQL_function($type, $one) {
  global $mySQL_which;
  foreach ($mySQL_which as $a)
    if ($type == $a[0] && $one == $a[1])
      return $a[2];
  return mySQL_invalid;
}

function mySQL_doc($resource, $type, $one) {
  global $mySQL_which;
  foreach ($mySQL_which as $a)
    if ($type == $a[0] && $one == $a[1])
      return str_replace('$resource', $resource, $a[3]);
  return '';
}
