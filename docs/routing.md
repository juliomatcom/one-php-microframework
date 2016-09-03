##ONE PHP Micro Framework Documentation
###Routing:

####Action on GET Request:
```php
//index.php file
require_once('src/OnePHP/one_framework.php');
$app = new \OnePHP\App();

$app->get('/',function() use ($app){//Action
    echo 'Hello world';
});
$app->listen();
```

####Action on POST Request with slugs: (same with PUT and DELETE)
#####Slugs are defined by {x} or :x  inside a Route    

 Examples   
```php
//$id_book will be the value passed on the URL
$app->put('/book/:id_book/',function($id_book) use ($app){
    //update...
});

$app->delete('/book/{id_book}/',function($id_book) use ($app){
    //delete...
});
```
####Respond all Request (if no match)
```php
$app->respond( function() use ( $app ){
    return $app->ResponseHTML('<p> This is a response with code 404. </p>', 404);
    });
```

####Generating new routes with getRoute
```php
<a href="<?php echo $app->generateRoute('/about'); ?>"> About </a>

<a href="<?php echo $app->generateRoute('/book/'.$id_book.'/edit'); ?>">
    Edit $book
</a>
```

###Next: [Controllers ](controllers.md "Using your controllers with One Framework")

######Contribute and improve this documentation.
######Click Edit and Fork the project.
