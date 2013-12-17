<?php
//
// Made by        db0
// Contact        db0company@gmail.com
// Website        http://db0.fr/
// Repo           https://github.com/db0company/invite
//

$methods =
    array(
          array('type' => 'POST',
                'resource' => 'services',
                'one' => false,
                'function' => 'addService',
                'auth_required' => false,
                'required_params' => array('service_name' => 'string',
                                           'password' => 'password'),
                'optional_params' => array('nb_invites' => 100),
                'response' => 'boolean',
                'doc' => 'Add a new service.',
                ),

          array('type' => 'DELETE',
                'resource' => 'services',
                'one' => true,
                'function' => 'removeService',
                'auth_required' => true,
                'required_params' => array('password' => 'password'),
                'optional_params' => array(),
                'response' => 'boolean',
                'doc' => 'Remove a service.',
                ),

          array('type' => 'GET',
                'resource' => 'invites',
                'one' => false,
                'function' => 'get1Invite',
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
                'function' => 'checkInvite',
                'auth_required' => false,
                'required_params' => array('service_name' => 'string'),
                'optional_params' => array(),
                'response' => 'boolean',
                'doc' => 'Check if the invite has been used or not.',
                ),

          array('type' => 'PUT',
                'resource' => 'invites',
                'one' => true,
                'function' => 'useInvite',
                'auth_required' => false,
                'required_params' => array('service_name' => 'string'),
                'optional_params' => array(),
                'response' => 'boolean',
                'doc' => 'Use this invite. It becomes invalid onces it has been used.',
                ),

          array('type' => 'DELETE',
                'resource' => 'invites',
                'one' => true,
                'function' => 'removeInvite',
                'auth_required' => true,
                'required_params' => array('service_name' => 'string',
                                           'password' => 'password'),
                'optional_params' => array(),
                'response' => 'boolean',
                'doc' => 'Cancels the given invite. Nobody will be able to use it.',
                ),

          array('type' => 'DELETE',
                'resource' => 'invites',
                'one' => false,
                'function' => 'removeAllInvites',
                'auth_required' => true,
                'required_params' => array('service_name' => 'string',
                                           'password' => 'password'),
                'optional_params' => array(),
                'response' => 'boolean',
                'doc' => 'Remove all the invites of a service.',
                ),

          );
