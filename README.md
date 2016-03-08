#One PHP MicroFramework
An extremely light-weight and small Restful Micro-Framework MVC for Web 2.0 in one file, based on Symfony and ExpressJS. Zero config.   

[![Build Status](https://travis-ci.org/juliomatcom/one-php-microframework.svg?branch=master)](https://travis-ci.org/juliomatcom/one-php-microframework)
[![Latest Stable Version](https://poser.pugx.org/julces/oneframework/v/stable)](https://packagist.org/packages/julces/oneframework)
[![Latest Unstable Version](https://poser.pugx.org/julces/oneframework/v/unstable)](https://packagist.org/packages/julces/oneframework)
[![License](https://poser.pugx.org/julces/oneframework/license)](https://packagist.org/packages/julces/oneframework)   

---
##Simplest usage:
```php
//index.php   
require_once('src/OnePHP/one_framework.php');
$app = new \OnePHP\App();

$app->get('/:name',function( $name ) use ( $app ){//Action
        echo "Hello $name";    
    });     
$app->listen();
```

###Install:
1- With [**Composer**](https://getcomposer.org/ "download Composer") or download Master [**ZIP**](https://github.com/juliomatcom/one-php-microframework/archive/master.zip "download One PHP Master version"):       
```     
composer create-project julces/oneframework
```
2- Include the **one_framework.php** in your project and  copy the **.htaccess** file in the **Root Folder** for use the **index.php** as your front controller. See **file structure**  [here](http://oneframework.net/docs/structure.md "File structure")  for more info.  
3- Run **App->listen()** after add some Actions

###Why use this tiny Microframework?
**One PHP** is perfect for you if you need write quickly **small** and **fast** Web 2.0 applications with:  
1- **Restful** Routes   
2- Easy and clean manage (GET, POST, PUT, DELETE...) **Requests**   
3- Restful **Response** with **HTTP Status Code** and custom **Headers**   
4- **PHP** native **Views**     
5- **No dependencies**, add extra libraries only when you need it.   
####Do not use One PHP for:
1- Full stack projects  
2- You need **built in** big libraries like Doctrine and others




###Basic Usage 2: Respond all Request (if no other match)
```php
$app->respond( function() use ( $app ){
    return $app->ResponseHTML('<p> This is a response with code 404. </p>', 404);
    });
```

##Read the [Documentation](http://oneframework.net/docs/ "See the official documentation in the One Micro Framework website")

###Contribute, is easy!
Found a **bug**, need **directions**
or just want to say hi ?        
Let me know, **Fork** the project, created an **issue** or **contact** me.

Follow [@juliomatcom](https://twitter.com/juliomatcom    "News and updates") to keep update

[http://oneframework.net/](http://oneframework.net/    "Official website")
