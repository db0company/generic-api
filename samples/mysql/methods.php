<?php
//
// Made by        db0
// Contact        db0company@gmail.com
// Website        http://db0.fr/
// Repo           https://github.com/db0company/generic-api
//

// CREATE TABLE IF NOT EXISTS `article` (
// 				      `id` int(11) NOT NULL AUTO_INCREMENT,
// 				      `title` varchar(255) NOT NULL,
// 				      `content` text NOT NULL,
// 				      PRIMARY KEY (`id`)
// 				      ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

$article_type = array('title' => 'string',
		      'content' => 'string');

$methods =
    array(

	  array('type'            => 'GET',
		'resource'        => 'article',
		'one'             => false,
		'response'        => $article_type,
		),

	  array('type'            => 'GET',
		'resource'        => 'article',
		'one'             => true,
		'response'        => $article_type,
		),

	  array('type'            => 'POST',
		'resource'        => 'article',
		'one'             => false,
		'response'        => $article_type,
		'required_params' => $article_type,
		),

	  array('type'            => 'PUT',
		'resource'        => 'article',
		'one'             => true,
		'response'        => $article_type,
		'optional_params' => array('title' => null,
					   'content' => null),
		),

	  array('type'            => 'DELETE',
		'resource'        => 'article',
		'one'             => false,
		'response'        => '',
		),

	  array('type'            => 'DELETE',
		'resource'        => 'article',
		'one'             => true,
		'response'        => '',
		),

          );

include_once("include.php");
$methods = setDefault($methods);
