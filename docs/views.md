##ONE PHP Framework Documentation
###Views:

###To add a view just create .php files in your a new folder project named /views/ then use Response to render it:
####Rendering a view
```php
// file: /controllers/book.php
// view is located in /views/books_list_view.php
$app->post('/books/',function() use ($app){
     $books = $this->getBooks();
     return $app->Response('books_list_view.php',array('books' => $books));
});
```
#####You can change the default directory of view in the constants of de Framework   
####View and translation
```php
// /views/home.php
// $app is pass as global variable to every View file
  <p>   <?php echo $app->trans('home_tittle'); ?> </p>
 <span> <?php echo $book->getAuthor(); ?> </span>


```
####The $app var and Vars passed  to Views are globally accesible from any view loaded by Response().

###[Keep reading documentation on Github](https://github.com/juliomatcom/one-php-framework/blob/master/docs/contents.md "See the official documentation of the One Framework")

#####This documentation is served in [oneframework.julces.com ](http://oneframework.julces.com/docs/ "More documentation of the One Framework")
######Contribute and improve this documentation.
######Click Edit and Fork the project.