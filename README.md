##One  <i>Micro PHP MVC Framework.</i>
######Only 1 file (less than 10KB). Not MVC required. Ready to go.
###Freatures:
####1- Route system (And generator for views)
####2- Translations (Available in controller and views)
####3- Response (load views)
#####Installation:
1- Include the one_framework.php in your project and  the .htaccess file in the root folder.    
2- Initialize the class, add some routes-action with get. (See the example bellow).    
3- Run listen.
  
```php
//index.php file    
require_once('one_framework.php');  
//load Framework    
$app = new OneFramework();      

$app->get('/',function() use ($app){//anonymous function    
    echo 'Hello world';     
    // return $app->Response('index.php',$data)     
});     
$app->listen();
```


####MVC style could look like this:
![MVC folders](http://i60.tinypic.com/ne6hhl.png "MVC folders")

######If you want to see the  /index.php/ in all URLS change the defined constant: <i> APP_NAME</i> in the Framework class and delete the .htaccess from the project.   


*Fell free to change everything you need and make a commit if you improve something.

[Follow me @juliomatcom](https://twitter.com/juliomatcom    "Follow me and get in touch")  
[http://oneframework.julces.com/](http://oneframework.julces.com/    "Official website")
