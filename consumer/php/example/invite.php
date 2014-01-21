<?php

include_once('../consumer.php');

function get_invite($service_name, $password, $unique = true) {
  return consume('http://invite.paysdu42.fr',
		 'invites', 'string',
		 'GET', null,
		 array('service_name' => $service_name,
		       'password' => $password,
		       'unique' => $unique));
}

function check_invite($service_name, $invite) {
  return consume('http://invite.paysdu42.fr',
                 'invites', 'string',
                 'GET', $invite,
                 array('service_name' => $service_name));
}

function use_invite($service_name, $invite) {
  return consume('http://invite.paysdu42.fr',
                 'invites', 'bool',
                 'PUT', $invite,
                 array('service_name' => $service_name));
}

if ($invite = get_invite('test', 'testtest'))
  echo $invite;
else
  echo 'Error: Could not get any invite';
echo "\n";
