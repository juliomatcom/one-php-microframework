##ONE PHP Framework Documentation
###Routing:

####Action on GET Request:
```php
//index.php file
require_once('one_framework.php');
$app = new OneFramework();

$app->get('/',function() use ($app){//Action
    echo 'Hello world';
});
$app->listen();
```

####Action on POST Request with slugs: (same with PUT and DELETE)
```php
//$id_book will be the value passed on the URL
$app->post('/book/{id_book}/update',function($id_book) use ($app){
    //save...
});
```
####Respond all Request (if no match)
```php
$app->respond(function() use ($app){
    echo 'Sorry this page does not exist';
});
```

####Generating new routes with getRoute 
```php
<a href="<?php echo $app->getRoute('/about'); ?>"> About </a>   

<a href="<?php echo $app->getRoute('/book/'.$id_book.'/edit'); ?>"> 
    Edit $book
</a>
```

###[Keep reading documentation on Github](https://github.com/juliomatcom/one-php-framework/blob/master/docs/contents.md "See the official documentation of the One Framework")

#####This documentation is served in [oneframework.net ](http://oneframework.net "More documentation of the One Framework")
######Contribute and improve this documentation.
######Click Edit and Fork the project.