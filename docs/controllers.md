##ONE PHP Micro Framework Documentation
###Controllers:

Include in your index.php (front controller) all your controllers files then just add some Actions.

####Using Restful Actions on Books
```php
// $id_book will be the value passed on the URL
// file: /controllers/main.php

//create
$app->post('/books/',function() use ($app){
     $handler = new BookHandler();
     $book = $handler->create( $app->getRequest() );
     
     return $app->Response('view.php',array('book' => $book));
});

$app->get('/books/{id_book}/',function($id_book) use ($app){
     $handler = new BookHandler();
     $book = $handler->get($id_book);
     
     return $app->Response('view.php',array('book' => $book));
});

$app->put('/books/{id_book}/',function($id_book) use ($app){
     $handler = new BookHandler();
     $book = $handler->update($id_book,$app->getRequest());
     
     return $app->Response('view.php',array('book' => $book));
});

$app->delete('/books/{id_book}/',function($id_book) use ($app){
     $handler = new BookHandler();
     $res = $handler->delete($id_book);
     
     return $app->Response('view.php',array('deleted' => $res));
});

```
You can read more about restful Actions in: 
[restapitutorial.com ](http://www.restapitutorial.com/lessons/httpmethods.html "restapitutorial.com")


###Next: [Views ](https://github.com/juliomatcom/one-php-microframework/blob/master/docs/views.md "Render views from controllers with One Framework")


#####This documentation is served in [oneframework.net ](http://oneframework.net/docs/ "More documentation of the One Framework")
######Contribute and improve this documentation.
######Click Edit and Fork the project.
