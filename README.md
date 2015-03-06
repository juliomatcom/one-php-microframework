##ONE <i> PHP Minimalist MVC Framework.</i>
Only 1 file (Easy readable). Not MVC required. Ready to go.  
Perfect fit in small projects and web services, only use what you need as you want.   
Keep your projects simple.
####Features:
#####1- Route system (And generator for views)
#####2- Easy and clean manage (GET, POST, PUT, DELETE) requests
#####3- Native localizations by URL (Available in controller and views)
#####3- Response (+load Views)
#####4- Inspired in Symfony and ExpressJS    
#####5- Zero config - Agile development

###Add to your project:
1-Install with Composer or download zip:        
```     
composer create-project julces/oneframework
``` 
2- Include the one_framework.php in your project and  copy the .htaccess file in the root folder for use the index.php as your front controller.     
3- Initialize the class, add some routes-action with get. (See the example bellow).    
4- Run listen.  

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

####Respond all Request (if no match)
```php
$app->respond(function() use ($app){
    echo 'Sorry this page does not exist';
});
```
##Read the [Documentation](https://github.com/juliomatcom/one-php-framework/blob/master/docs/contents.md "See the official documentation of the One Framework")

######If you want to see the  /index.php/ in all URLS change the defined constant: <i> APP_NAME</i> in the Framework class and delete the .htaccess from the project.

###Contribute, is easy!
Found a bug? Have a good idea for improving One Php Framework?      
Want to help with the documentation or translations?        
Let us know, fork the project, created an issue or contact us.

[Follow me @juliomatcom](https://twitter.com/juliomatcom    "Follow me and get in touch")  
[http://oneframework.julces.com/](http://oneframework.julces.com/    "Official website")
