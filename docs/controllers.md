##ONE PHP Framework Documentation
###Controllers:

####MVC style could look like this:
![MVC folders](http://i60.tinypic.com/ne6hhl.png "MVC folders")

###Include in your index.php (front controller) all your controllers files then just add some Actions like this:
```php
// $id_book will be the value passed on the URL
// file: /controllers/main.php
$app->post('/book/{id_book}/update',function($id_book) use ($app){
     $book = $this->upd($id_book);
     return $app->Response('view.php',array('book' => $book));
});
```

###Next: [Views ](https://github.com/juliomatcom/one-php-framework/blob/master/docs/views.md "Render views from controllers with One Framework")

###[Keep reading documentation on Github](https://github.com/juliomatcom/one-php-framework/blob/master/docs/contents.md "See the official documentation of the One Framework")

#####This documentation is served in [oneframework.net ](http://oneframework.net/docs/ "More documentation of the One Framework")
######Contribute and improve this documentation.
######Click Edit and Fork the project.
