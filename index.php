<?php
//One Micro Framework - Hello World
//remember enable the .htacess in this folder
require_once('one_framework.php');

    //load Micro Framework
    $app = new OneFramework();

    $app->get('/',function() use ($app){//Action on the Root URL
        echo 'Hello world';
    });

    //test with slug in URL
    $app->get('/{name}',function($name) use ($app){
        echo "Hello $name";
    });

    //Run
    $app->listen();