##ONE PHP Micro Framework Documentation
Just 2 simple class to help you develop easy and fast websites and webservices for webs 2.0 and allow you to create your own microframework easily.

#### Table of contents:
####1- [Folder structure ](https://github.com/juliomatcom/one-php-microframework/blob/master/docs/structure.md "Folder structure Normal or MVC")
####2- [Routing ](https://github.com/juliomatcom/one-php-microframework/blob/master/docs/routing.md "Start with routings")
####3- [Controllers ](https://github.com/juliomatcom/one-php-microframework/blob/master/docs/controllers.md "Using your controllers with One Framework")
####4- [Views ](https://github.com/juliomatcom/one-php-microframework/blob/master/docs/views.md "Render views from controllers with One Framework")
####5- [More](https://github.com/juliomatcom/one-php-microframework/blob/master/docs/more.md "More documentation of the One Framework")


####Simplest example:
```php
    //index.php file
    require_once('src/OnePHP/one_framework.php');
    $app = new \OnePHP\App();

    $app->get('/',function() use ($app){//Action
        echo 'Hello world';
    });
    $app->listen();
```


#####This documentation is served in [oneframework.net ](http://oneframework.net/docs/ "More documentation of the One Framework")
######Contribute and improve this documentation.
######Click Edit and Fork the project.