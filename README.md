##One  <i>Micro PHP MVC Framework.</i>
Only 1 file (easy readable, less 10kb). Not MVC required. Ready to go.    
Perfect fit in small projects and web services, only use what you need as you want.
###Freatures:
####1- Route system (And generator for views)
####2- Translations by URL automatic (Available in controller and views)
####3- Response (load views)
####4- Inspired in Symfony and ExpressJS    
#####Installation:
1- Include the one_framework.php in your project and  the .htaccess file in the root folder.    
2- Initialize the class, add some routes-action with get. (See the example bellow).    
3- Run listen.
  
```php
//index.php file    
require_once('one_framework.php');  
//load Framework    
$app = new OneFramework();      

$app->get('/',function() use ($app){//Action
    echo 'Hello world';     
});     
$app->listen();
```


####MVC style could look like this:
![MVC folders](http://i60.tinypic.com/ne6hhl.png "MVC folders")



```php
// /controllers/main.php    
//Dynamic route with 1 variable in the URL 
$app->get('/book/{id_book}/edit',function($id_book) use ($app){ 
$book = getBook($id_book);
     return $app->Response('view_path.php',array('book' => $book));
});   

```

####View and translation
```php
// /views/home.php
// $app is pass as global variable to every View file
 <p><?php echo $app->trans('home_tittle'); ?></p>
```
The framework $app is globally accesible from any view loaded by Response().
#### Translation file look like this
```txt
// /translations/home.en.txt
home_tittle: My website Title
home_menu: Menu
```
Every file inside /translations/ folder will be loaded automatically.
######If you want to see the  /index.php/ in all URLS change the defined constant: <i> APP_NAME</i> in the Framework class and delete the .htaccess from the project.   
*Fell free to change everything you need and make a commit if you improve something.  

[Follow me @juliomatcom](https://twitter.com/juliomatcom    "Follow me and get in touch")  
[http://oneframework.julces.com/](http://oneframework.julces.com/    "Official website")
