<?php
//load
require_once(__DIR__.'/../../src/OnePHP/one_framework.php');

/**
 * Class OneFrameworkTest
 * @author Julio Cesar Martin - juliomatcom@gmail.com
 * @version 0.1.0
 */
class OneFrameworkTest extends PHPUnit_Framework_TestCase
{

    /*
     * Try the match routes system
     */
    public function testRouteMatch()
    {
        //static route
        $this->tryMatch('/','/', array());

        /* Dynamic routes */
        //with :slug
        $this->tryMatch('/hello/:name','/hello/juliomatcom', array(
            'name' => 'juliomatcom'
        ));
        $this->tryMatch('/hello/:name/age/:age','/hello/juliomatcom/age/25', array(
            'name' => 'juliomatcom',
            'age' => 25
        ));

        //with {slug}
        $this->tryMatch('/hello/{name}','/hello/juliomatcom', array(
            'name' => 'juliomatcom'
        ));
        $this->tryMatch('/hello/{name}/age/{age}','/hello/juliomatcom/age/25', array(
            'name' => 'juliomatcom',
            'age' => 25
        ));
    }

    public function  testMethod(){
        $app = new \OnePHP\App();

        $func = function() {
            return true;
        };
        //default HTTP Requests in OneFramework
        $app->get('/get', $func);
        $app->post('/post', $func);
        $app->put('/put', $func);
        $app->delete('/delete', $func);

        //check if routes are found
        $this->assertEquals('/get', $app->getRoutes()['GET'][0]->route );
        $this->assertEquals('/post', $app->getRoutes()['POST'][0]->route );
        $this->assertEquals('/put', $app->getRoutes()['PUT'][0]->route );
        $this->assertEquals('/delete', $app->getRoutes()['DELETE'][0]->route );
    }

    private function tryMatch($route, $uri, array $expected_slugs = array() ){
        $testFunc =  function ( $name ) { };

        $routeObj = new \OnePHP\Route( $route , $testFunc);

        $uri_segments = preg_split('/[\/]+/',$uri,null,PREG_SPLIT_NO_EMPTY);

        $route_segments = preg_split('/[\/]+/',$routeObj->route,null,PREG_SPLIT_NO_EMPTY);
        $slugs = array();

        $matched = \OnePHP\CoreFramework::CompareSegments($uri_segments,$route_segments,$slugs);

        //function was found
        $this->assertEquals(true, $matched, "Cant match this route: '$route' with '$uri'\n");
        //slugs values was properly saved
        $this->assertEquals($expected_slugs, $slugs, 'Final slugs does not match');
    }

}