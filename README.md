##One PHP Micro Framework:
One PHP is An extremely light-weight and small Restful Micro-Framework MVC for Web 2.0 in one file, based on Symfony and ExpressJS. Zero config.   

[![Build Status](https://travis-ci.org/juliomatcom/one-php-microframework.svg?branch=master)](https://travis-ci.org/juliomatcom/one-php-microframework)
[![Latest Stable Version](https://poser.pugx.org/julces/oneframework/v/stable)](https://packagist.org/packages/julces/oneframework)
[![Latest Unstable Version](https://poser.pugx.org/julces/oneframework/v/unstable)](https://packagist.org/packages/julces/oneframework)
[![License](https://poser.pugx.org/julces/oneframework/license)](https://packagist.org/packages/julces/oneframework)   
######Required PHP >= 5.4
####Simplest usage:
```php
//index.php file    
require_once('src/OnePHP/one_framework.php');
$app = new \OnePHP\App();

$app->get('/:name',function( $name ) use ( $app ){//Action
        echo $name != '' ? 'Hello world' : "Hello $name";     
    });     
$app->listen();
```

###What is this good for?
#####One PHP is perfect for you if you need write quickly small and fast Web 2.0 applications with:
#####1- Restful Routes 
#####2- Easy and clean manage (GET, POST, PUT, DELETE...) Requests
#####3- Restful Responses with HTTP Status Code and custom Headers
#####4- PHP native Views   
#####5- Extra libraries only when you need it, keep things simple   
###Do not use One PHP for:
#####1- Full stack projects
#####2- You need "built in" database libraries like Doctrine and others

###Add to your project:
1-Install with Composer or download Master zip:        
```     
composer create-project julces/oneframework
``` 
2- Include the one_framework.php in your project and  copy the .htaccess file in the Root Folder for use the index.php as your front controller.     
Verify that your virtual host point to the folder with the .htacess and index.php file inside       
3- Initialize OneFramework, add Actions. (See more examples and documentation).    
4- Run listen and open http://yourVirtualHost/


####Basic Usage 2: Respond all Request (if no match)
```php
$app->respond( function() use ( $app ){
    return $app->ResponseHTML('<p> This is a response with code 404. </p>', 404);
    });
```

##Read the [Documentation](http://oneframework.net/docs/ "See the official documentation in the One Micro Framework website")

###Contribute, is easy!
Found a bug? Have a good idea for improving One PHP Micro Framework?
Want to help with the documentation or translations?        
Let us know, fork the project, created an issue or contact us.

[Follow @juliomatcom to keep update](https://twitter.com/juliomatcom    "News and updates")

[http://oneframework.net/](http://oneframework.net/    "Official website")
