<?php
//One Micro Framework - Hello World
//remember enable the .htacess in this folder
require_once('src/OnePHP/one_framework.php');

/*
* Remember remove this examples to avoid collisions in routes
*/
    //load Micro Framework with debug enabled
    $app = new \OnePHP\App();

    $app->get('/', function() use ( $app ){//Action on the Root URL
        echo 'Hello world';
    });

    //test with slug in URL ( ':name' = '{name}' )
    $app->get('/:name', function( $name ) use ( $app ){
        return $app->Response("<h1> Hello <small>$name</small> </h1>");
    });

    $app->respond( function() use ( $app ){
        return $app->Response('This is a response with code 404.',array(),404);
    });

    //Run
    $app->listen();