<?php
//One framework - Hello World
//remember enable the .htacess in this folder
require_once('one_framework.php');

    //load Framework
    $app = new OneFramework();

    $app->get('/',function() use ($app){//Action on the Root URL
        echo 'Hello world';
    });

    //Run
    $app->listen();