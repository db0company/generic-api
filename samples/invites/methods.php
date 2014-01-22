<?php
//
// Made by        db0
// Contact        db0company@gmail.com
// Website        http://db0.fr/
// Repo           https://github.com/db0company/generic-api
//

function checkPassword($params) {
  $db = mySQL_db();
  $r = $db->prepare('SELECT * FROM services WHERE name=? AND password=md5(?)');
  if (!(mySQL_execute($r, array($params['service_name'], $params['password']))))
    return array(500);
  if ($r->rowCount() === 0)
    return false;
  return true;
}

$methods =
    array(
          array('type' => 'POST',
                'resource' => 'services',
                'one' => false,
                'function' => function($resource, $one, $params) {
		  $db = mySQL_db();
		  $r = $db->prepare('INSERT INTO services(name, password) VALUES(?, md5(?))');
		  if (!(mySQL_execute($r, array($params['service_name'], $params['password']))))
		    throw new Exception(500);
		  $r = $db->prepare('INSERT INTO invites(service_name, invite, sent, used) VALUES(?, ?, false, false)');
		  for ($i = 0; $i < $params['nb_invites']; $i++) {
		    $invite = uniqid().uniqid().uniqid();
		    if (!(mySQL_execute($r, array($params['service_name'], $invite))))
		      throw new Exception(500);
		  }
		  return true;
		},
                'auth_required' => false,
                'required_params' => array('service_name' => 'string',
                                           'password' => 'password'),
                'optional_params' => array('nb_invites' => 100),
                'response' => 'boolean',
                'doc' => 'Add a new service.',
                ),

          array('type' => 'GET',
                'resource' => 'invites',
                'one' => false,
                'function' => function($resource, $one, $params) {
		  $db = mySQL_db();
		  if (!checkPassword($params))
		    throw new Exception(403);
		  $r = $db->prepare('SELECT invite FROM invites WHERE service_name=? AND used=false '
					  .($params['unique'] ? 'AND sent=false ' : '')
					  .'ORDER BY rand() LIMIT 1');
		  if (!($r->execute(array($params['service_name']))))
		    throw new Exception(500);
		  if (!($r->rowCount()))
		    throw new Exception(202);
		  $invite = $r->fetch();
		  $invite = $invite[0];
		  $r_update = $db->prepare('UPDATE invites SET sent=true WHERE service_name=? AND invite=?');
		  if (!(mySQL_execute($r_update, array($params['service_name'], $params['id']))))
		    throw new Exception(500);
		  return $invite;
		},
                'auth_required' => true,
                'required_params' => array('service_name' => 'string',
                                           'password' => 'password'),
                'optional_params' => array('unique' => true),
                'response' => 'string',
                'doc' => 'Get one invite that has never been sent before, or any invite if the "unique" param is set to false.',
                ),

          array('type' => 'GET',
                'resource' => 'invites',
                'one' => true,
                'function' => function($resource, $one, $params) {
		  $db = mySQL_db();
		  $r = $db->prepare('SELECT * FROM invites WHERE used=false AND service_name=? AND invite=?');
		  if (!(mySQL_execute($r, array($params['service_name'], $params['id']))))
		    throw new Exception(500);
		  return $r->rowCount() != 0;
		},
                'auth_required' => false,
                'required_params' => array('service_name' => 'string'),
                'optional_params' => array(),
                'response' => 'boolean',
                'doc' => 'Check if the invite has been used or not.',
                ),

          array('type' => 'PUT',
                'resource' => 'invites',
                'one' => true,
                'function' => function($resource, $one, $params) {
		  $db = mySQL_db();
		  $r = $db->prepare('UPDATE invites SET used=true WHERE service_name=? AND invite=? AND used=false');
		  if (!(mySQL_execute($r, array($params['service_name'], $params['id']))))
		    throw new Exception(500);
		  return $r->rowCount() != 0;
		},
                'auth_required' => false,
                'required_params' => array('service_name' => 'string'),
                'optional_params' => array(),
                'response' => 'boolean',
                'doc' => 'Use this invite. It becomes invalid onces it has been used.',
                ),

          // array('type' => 'DELETE',
          //       'resource' => 'services',
          //       'one' => true,
          //       'function' => 'removeService',
          //       'auth_required' => true,
          //       'required_params' => array('password' => 'password'),
          //       'optional_params' => array(),
          //       'response' => 'boolean',
          //       'doc' => 'Remove a service.',
          //       ),

          // array('type' => 'POST',
          //       'resource' => 'invites',
          //       'one' => false,
          //       'function' => 'generateInvites',
          //       'auth_required' => true,
          //       'required_params' => array('service_name' => 'string',
	  // 				   'password' => 'password'),
          //       'optional_params' => array('nb_invites' => 100),
          //       'response' => 'boolean',
          //       'doc' => 'Generate a new bunch of invites associated to this service.',
          //       ),

          // array('type' => 'DELETE',
          //       'resource' => 'invites',
          //       'one' => true,
          //       'function' => 'removeInvite',
          //       'auth_required' => true,
          //       'required_params' => array('service_name' => 'string',
          //                                  'password' => 'password'),
          //       'optional_params' => array(),
          //       'response' => 'boolean',
          //       'doc' => 'Cancels the given invite. Nobody will be able to use it.',
          //       ),

          // array('type' => 'DELETE',
          //       'resource' => 'invites',
          //       'one' => false,
          //       'function' => 'removeAllInvites',
          //       'auth_required' => true,
          //       'required_params' => array('service_name' => 'string',
          //                                  'password' => 'password'),
          //       'optional_params' => array(),
          //       'response' => 'boolean',
          //       'doc' => 'Remove all the invites of a service.',
          //       ),

          );
