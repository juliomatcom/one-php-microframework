##ONE PHP Micro Framework Documentation
###Views:

###To add a view just create .php files in your a new folder project named /views/ then use Response to render it:
####Rendering a view
```php
// file: /src/controllers/book.php
// view is located in /src/views/books_list_view.php
$app->post('/books/',function() use ($app){
     $books = $this->getBooks();
     return $app->Response('books_list_view.php',array('books' => $books));
});
```
####You can serve static files in your views files just locating them in the top of Root folder '/'  like: ( See [Folder structure ](https://github.com/juliomatcom/one-php-microframework/blob/master/docs/structure.md "Folder structure Normal or MVC") )

```html
<!-- file: /views/home.php -->
    <!-- Bootstrap Core CSS -->
    <link href="/public/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="/public/css/one-page-wonder.css" rel="stylesheet">
```
#####You can change the default directory of view in the constants of de Framework   
The $app var and Vars passed  to Views are globally accesible from any view loaded by Response().

###Next: [More](https://github.com/juliomatcom/one-php-microframework/blob/master/docs/more.md "More documentation of the One Framework")

#####This documentation is served in [oneframework.net ](http://oneframework.net/docs/ "More documentation of the One Framework")
######Contribute and improve this documentation.
######Click Edit and Fork the project.