<?php
//
// Made by        db0
// Contact        db0company@gmail.com
// Website        http://db0.fr/
// Repo           https://github.com/db0company/generic-api
//

$methods =
    array(

//         array('type'            => GET (default), POST, PUT or DELETE
//               'resource'        => the name of the resource you're working on (required)
//                                    examples: "user", "article", ...
//               'one'             => true if you're working on one single element
//                                    false if you're working on all (default)
//               'function'        => the function to be called when the method is called
//                                    function parameters:
//                                      - the resource
//                                      - the id of the element if one is true, false otherwise
//                                      - the parameters
//                                    default: handles classic database actions
//               'auth_required'   => true or false (default)
//               'optional_params' => an array of name => default value (the type is guessed from the value)
//               'required_params' => an array of name => type (as strings)
//                                    know types: string, int, bool, password
//               'response'        => the type of the response, used for documentation only
//                                    default: string
//               'doc'             => a sentence that explains what the method does
//                                    default: classic database actions
//               ),

          );

include_once("include.php");
$methods = setDefault($methods);
