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

if ($invite = get_invite('test', 'testtest'))
  echo $invite;
else
  echo 'Error: Could not get any invite';
echo "\n";
