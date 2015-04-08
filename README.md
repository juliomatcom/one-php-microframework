##One PHP Micro Framework:
One PHP is a lightweight Rest-first micro framework in one file with 2 simple class to help you develop easy and fast websites and webservices for webs 2.0 and allow you to create your own microframework easily.

####Basic Usage:
```php
//index.php file    
require_once('one_framework.php');  
$app = new OneFramework();      

$app->get('/',function() use ($app){//Action
    echo 'Hello world';     
});     
$app->listen();
```

####Features:
#####1- Route system (And generator for views)
#####2- Easy and clean manage (GET, POST, PUT, DELETE) requests
#####3- Response + Status Code & Headers (+load Views)
#####4- Inspired in Symfony and ExpressJS    
#####5- Zero config - Agile development

###Add to your project:
1-Install with Composer or download zip:        
```     
composer create-project julces/oneframework
``` 
2- Include the one_framework.php in your project and  copy the .htaccess file in the Root Folder for use the index.php as your front controller.     
Verify that your virtual host point to the folder with the .htacess and index.php file inside       
3- Initialize the class, add some routes-action with $app->get(...),etc. (See the examples).    
4- Run listen and open http://yourVirtualHost/


####Basic Usage 2: Respond all Request (if no match)
```php
$app->respond(function() use ($app){
    return $app->Response('Sorry this page does not exist',array(),404);
});
```

##Read the [Documentation](http://oneframework.net/docs/ "See the official documentation in the One Micro Framework website")

###Contribute, is easy!
Found a bug? Have a good idea for improving One PHP Micro Framework?
Want to help with the documentation or translations?        
Let us know, fork the project, created an issue or contact us.

[Follow @OnePHP](https://twitter.com/OnePHP    "News and updates")

[http://oneframework.net/](http://oneframework.net/    "Official website")
