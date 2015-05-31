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
        echo "<h1> Hello <small> $name </small> </h1>";
    });

    //simple Json Response example
    $app->get('/json/:name', function( $name ) use ( $app ){
        return $app->JsonResponse(array('name' => $name));
    });

    $app->respond( function() use ( $app ){
        return $app->ResponseHTML('<p> This is a response with code 404. </p>', 404);
    });

    //Run
    $app->listen();