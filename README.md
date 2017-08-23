# One PHP MicroFramework
An extremely light-weight and small Restful Micro-Framework MVC for Web 2.0 that doesn't get in your way, based on Symfony and ExpressJS. No Config.

[![Build Status](https://travis-ci.org/juliomatcom/one-php-microframework.svg?branch=master)](https://travis-ci.org/juliomatcom/one-php-microframework)
[![Latest Stable Version](https://poser.pugx.org/julces/oneframework/v/stable)](https://packagist.org/packages/julces/oneframework)
[![Latest Unstable Version](https://poser.pugx.org/julces/oneframework/v/unstable)](https://packagist.org/packages/julces/oneframework)
[![License](https://poser.pugx.org/julces/oneframework/license)](https://packagist.org/packages/julces/oneframework)

### :loudspeaker: *I stopped developing new features for OnePHP. If you feel like you have the time, you are welcome to [contact me](https://twitter.com/juliomatcom) or [PR](https://github.com/juliomatcom/one-php-microframework/pulls) and keep this cool framework up to date.*
---
## Simplest usage:
```php
// index.php
require_once('src/OnePHP/one_framework.php');
$app = new \OnePHP\App();

$app->get('/:name',function( $name ) use ( $app ){//Action
  echo "Hello $name";
});
$app->listen();
```

### Install:
1- With [**Composer**](https://getcomposer.org/ "download Composer") or download Master [**ZIP**](https://github.com/juliomatcom/one-php-microframework/archive/master.zip "download One PHP Master version"):
```
composer create-project julces/oneframework
```   
2- Include **one_framework.php** in your project and copy the **.htaccess** file in the **Root Folder** to use the **index.php** as your front controller. See **file structure**  [here](docs/structure.md)  for more info.  
3- Run **App->listen()** after adding some Actions  

### Why use this tiny Microframework?
**One PHP** is perfect for you if you need to quickly write **small** and **fast** Web 2.0 applications with:   
1- **Restful** Routes   
2- Easy and clean (GET, POST, PUT, DELETE...) **Requests** management   
3- Restful **Response** with **HTTP Status Code** and custom **Headers**   
4- **PHP** native **Views**   
5- **No dependencies**, add extra libraries only when you need it.  
#### Do not use One PHP if:
1- You are building big full stack projects   
2- You need big **built-in** libraries like Doctrine and others   




### Basic Usage 2: Respond to all Requests (if no other match)
```php
$app->respond( function() use ( $app ){
  return $app->ResponseHTML('<p> This is a response with code 404. </p>', 404);
});
```

## Read the [Documentation](docs/contents.md "Documentation")

### Contribute, it's easy!
Found a **bug**, need **directions**
or just want to say hi ?    
Let me know, **Fork** the project, create an **issue** or **contact** me.   

Follow [@juliomatcom](https://twitter.com/juliomatcom    "News and updates") to keep up to date

[MIT @juliomatcom](http://licsource.com/mit?name=Julio Cesar Martin&year=2016&email=juliomatcom@gmail.com&url=http://julces.com/)
