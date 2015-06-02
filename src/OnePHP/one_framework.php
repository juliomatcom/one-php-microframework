<?php
namespace OnePHP;

const ENV_DEV = 0;
const ENV_PROD = 1;
const APP_NAME = ''; //add front controller to URL (change only if you know what are you doing)


/**
 * Class CoreFramework
 * @version 0.2.0
 * This is the main components you need for your own microframework for web 2.0
 * OneFramework extends this Class
 * @author Julio Cesar Martin
 * juliomatcom@yandex.com
 */
abstract class CoreFramework{
    //Main request Object
    protected $request;
    //routes Array like routes['REQUEST_METHOD'] = array(ObjRoute1,ObjRoute2...)
    protected $routes = array();

    public function __construct(){
        $this->request = Request::createFromGlobals();
    }

    //Most popular HTTP Methods
    public abstract function get($uri,callable $callback);
    public abstract function post($uri,callable $callback);
    public abstract function put($uri,callable $callback);
    public abstract function delete($uri,callable $callback);

    /**
     * Start listen for requests
     */
    public abstract  function listen();

    /**
     * Get request Object
     * @return Request class
     */
    public function getRequest(){
        return $this->request;
    }

    /**
     * Set response HTTP Status Code
     * @param int $status default: OK 200
     * @return int status code sent
     */
    public function setStatusCode($status = 200){
        if ($status != 200){
            if (!function_exists('http_response_code'))//PHP < 5.4
            {//send header
                header('X-PHP-Response-Code: '.$status, true, $status);
            }
            else return http_response_code($status);
        }
        return $status;
    }

    /**
     *********** CORE FUNCTIONS ***********
     */

    /**
     * Traverse the routes and match the request, execute the callback
     * @param string $method REQUEST_METHOD
     * @param array $routes Routes Objects
     * @param array $slugs Add any Slugs value found in the Request path (order matters)
     * @return bool true if Route was found and callback executed, false otherwise
     */
    protected function traverseRoutes($method = 'GET',array $routes,array &$slugs){
        if (isset($routes[$method])){
            foreach($routes[$method] as $route)
                if($func = $this->processUri($route,$slugs)){
                    //call callback function with params in slugs
                    call_user_func_array($func,$slugs);
                    return true;
                }
        }
        return false;
    }

    protected  function getSegment($segment_number){
        $uri = $this->request->getRequestedUri();
        $uri_segments = preg_split('/[\/]+/',$uri,null,PREG_SPLIT_NO_EMPTY);

        return isset($uri_segments[$segment_number]) ? $uri_segments[$segment_number] : false;
    }

    private function processUri($route,&$slugs = array()){
        $url =$this->request->getRequestedUri();
        $uri = parse_url($url, PHP_URL_PATH);
        $func = $this->matchUriWithRoute($uri,$route,$slugs);
        return $func ? $func : false;
    }

    static function matchUriWithRoute($uri,$route,&$slugs){
        $uri_segments = preg_split('/[\/]+/',$uri,null,PREG_SPLIT_NO_EMPTY);

        $route_segments = preg_split('/[\/]+/',$route->route,null,PREG_SPLIT_NO_EMPTY);

        if (CoreFramework::compareSegments($uri_segments,$route_segments,$slugs)){
            //route matched
            return $route->function; //Object route
        }
        return false;
    }

    /**  Match 2 uris
     * @param $uri_segments
     * @param $route_segments
     * @return bool
     */
     static function CompareSegments($uri_segments,$route_segments,&$slugs){

        if (count($uri_segments) != count($route_segments)) return false;

        foreach($uri_segments as $segment_index => $segment){
            $segment_route = $route_segments[$segment_index];
            //different segments must be an {slug} | :slug
            $is_slug = preg_match('/^{[^\/]*}$/',$segment_route) || preg_match('/^:[^\/]*/',$segment_route,$matches);

            if ($is_slug)//Note php does not support named parameters
                $slugs[ str_ireplace(array(':', '{', '}'), '', $segment_route) ] = $segment;//save slug key => value
            else if($segment_route != $segment && $is_slug != 1) return false;

        }
        //match with every segment
        return true;
    }
}

/**
 * One PHP MVC Micro Framework
 * @version 0.5.3
 * @author Julio Cesar Martin
 * juliomatcom@yandex.com
 * Twitter @OnePHP
 * Contribute to the project in Github
 * http://oneframework.net/
 *
 * Class App
 * Controllers must be in APP_DIR/controllers
 * Views must be in APP_DIR/views
 * Assets must be in APP_DIR/public/
 */
class App extends CoreFramework{
    //instances vars and predefined configs
    protected $db;
    protected $prod = false;

    /**
     * Initialize Framework Core
     * @param bool $prod Enviroment, set to false for Enable Debugging
     */
    public function __construct($prod = false){
        parent::__construct();

        define('APP_DIR', $this->getRootDir() .'/../'); //if your project is in src/ like in documentation, if not correct this
        define('VIEW_DIR', APP_DIR .'views/');
        define('CONTROLLER_DIR', APP_DIR .'controllers/');
        define('VIEWS_ROUTE', APP_DIR .'views/');//deprecated since 0.4
        define('CONTROLLERS_ROUTE', APP_DIR .'controllers/');//deprecated since 0.4

        $this->setEnvironment($prod);
    }

    /*
     * Get framework Directory
     */
    public function getRootDir()
    {
        return __DIR__;
    }

    /**
     * Change environment to prod or not
     * @param $prod bool
     */
    public function  setEnvironment($prod = false){
        $this->prod = $prod ? ENV_PROD : ENV_DEV;
    }

    /**
     * Process the request and Return a Response
     * @param $uri string for the Route example: /book/{number}/edit
     * @param callable $function executable
     */
    public function get($uri,callable $callback){
        //save route and function
        $this->routes['GET'][] = new Route($uri,$callback);
    }

    /**
     * Process a POST Request
     * @param $uri string
     * @param callable $function executable
     */
    public function post($uri,callable $callback){
        $this->routes['POST'][] = new Route($uri,$callback);
    }

    /**
     * Process a PUT Request
     * @param $uri string
     * @param callable $function executable
     */
    public function put($uri,callable $callback){
        $this->routes['PUT'][] = new Route($uri,$callback);
    }

    /**
     * Process a DELETE Request
     * @param $uri string
     * @param callable $function executable
     */
    public function delete($uri,callable $callback){
        $this->routes['DELETE'][] = new Route($uri,$callback);
    }

    /**
     * Process all Request
     * @param $uri string
     * @param callable $function executable
     */
    public function respond(callable $callback){
        $this->routes['respond'] = new Route('',$callback);
    }

    /**
     * Look for match request in routes, execute the callback function
     */
    public function listen(){
        $slugs = array();

        $run = $this->traverseRoutes($this->request->getMethod(),$this->routes,$slugs);

        if(!$run && (!isset($this->routes['respond']) || empty($this->routes['respond']))){
            return $this->error("Route not found for Path: '{$this->request->getRequestedUri()}' with HTTP Method: '{$this->request->getMethod()}'. ", 1 );
        }
        else if(!$run){ //respond for all request;
            $callback = $this->routes['respond']->function;
            $callback();
        }
        return true;
    }

    public function getRoutes(){
        return $this->routes;
    }
    /**
     * Create a route to the app
     * @param $uri
     * @return string new URL
     */
    public function generateRoute($uri){
        return (APP_NAME != '') ?('/'.APP_NAME.$uri) : $uri;
    }

    public function getRoute($uri){//deprecated since 0.5
        return $this->generateRoute($uri);
    }

    /**
     * Get current enviroment
     * @return bool True if Production is ON
     */
    public function getEnvironment(){
        return $this->prod ? ENV_PROD : ENV_DEV;
    }

    public function Redirect($href){
        echo header('Location: '.$href);
    }

    /**
     * Return a new HTTP response.
     * @param string $view_filename Source to the file
     * @param array $vars Data to pass to the View
     * @param array $headers Http Headers
     */

    /**
     * @param string $filename src or content
     * @param array $vars Data to pass to the View
     * @param int $status Set the response status code.
     * @param array $headers Set response headers.
     * @param int $asText Echo as text
     */
    public function Response($filename = '', array $vars = array(), $status = 200, array $headers = array(),$asText = 0){
        $this->setStatusCode($status);

        if (count($headers)){//add extra headers
            $this->addCustomHeaders($headers);
        }
        //pass to the view
        if (!$asText){
            $view = new View(VIEWS_ROUTE.$filename, $vars, $this);
            $view->load();
        }
        else echo $filename;
    }

    public function ResponseHTML($html = '', $status = 200, array $headers = array()){
        return $this->Response($html, array(), $status, $headers, true);
    }

    /**
     * Send json encoded Response
     * @param mixed $data
     * @param int $status
     * @param array $headers
     */
    public function JsonResponse($data = null, $status = 200, array $headers = array() ){
        $this->setStatusCode($status);

        header('Content-Type: application/json');//set content type to Json
        if (count($headers)){//add extra headers
            $this->addCustomHeaders($headers);
        }

        echo json_encode($data);
    }


    private function addCustomHeaders(array $headers = array()){
        foreach($headers as $key=>$header){
            header($key.': '.$header);
        }
    }

    /**
     * Show framework's errors
     * @param string $msg
     * @param int $number
     */
    public function error($msg = '', $number = 0){
        $this->setStatusCode(500);//internal server error code

        if ($this->getEnvironment() == ENV_PROD){
            echo "<h2>:( </h2>
                  <h3>Sorry there is a problem with this request.</h3>
                  <p>Please try later or contact us.</p>";
        }
        else{//debug enable
            echo
                "<img alt='One PHP' src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGkAAABgCAYAAAAejVzyAAAABGdBTUEAALGPC/xhBQAAAAFzUkdCAK7OHOkAAAAgY0hSTQAAeiYAAICEAAD6AAAAgOgAAHUwAADqYAAAOpgAABdwnLpRPAAAAAZiS0dEAP8A/wD/oL2nkwAAAAlwSFlzAAAuIwAALiMBeKU/dgAAE4dJREFUeNrtnXt4VOWdxz+TSUgghMsSRMAaQrUK1tpW661uo7OtRUFtve2qvWq7uKZGvNJn17Wl2263KxcNYm1BXZe6W2q9bLXecKOwbldwuyqgFbGGYBEUQQyBkGQys39835PzzsmZyZnMJSeQ7/PkSTJz5lze7/v+7u9vIhwkWLKqxvlzFDAVmA58EjgKOAw4FBgJlANlQALoAPYA7wNvAxuBdcB64I/AbiBZX9dS0HuPDPTgFRIWMeOAzwB1wCnAx4A/Q4Rkiy5gFyJpDdAEvAi8R4EIOyBJMuSUAh8HvgycDRwDDC/A5TqAN4DHgYeAl4FOgHwRdkCRZMgpA04GvgHMBCYU8RZ2Ak8DdwPPIwJzJuuAIMmQEwGOBa4CLkLibKDwIfAo0Aj8HkjkQtSgJsnSOWOAK4AG4PCBvi8L24C7gJ8CO6B/q2rQkmQRdDzwD8AXkB4KGxLAKuDvgd/RD+NiUJJk6Z6LEEFTB/qeAuAdYB5wH9CRDVGDjiRD0HDgOmAuUDXQ95QF9iE99WOgNShRg4okQ9Bo4HvIQMjGz/kAeA35OBOBaUDlADxGHK2mucDOIEQNGpIMQVVoFl4JRAN+NInM4h8BLwH7UWThNGRonI5EZzGRAO4HrkVme0aDoqTIN9cvGIJGIJk+m+AEAawGvgX8F9BWX9cSR+Gcx4CLge8AGxCZxUIJcBnwExSmyojQryRDUBS4AfgBMCyLj3cAl6JIQK/ZalmINYj8b6IYXrHQDcwHbgE6062mUJNkDeJfIV9jTJanaAbOAFrmz7vefuYkQHNTg32dEmTOzwHOo3j6ap+55lLwF3uhFXcWQcehFTSmnwOwz/PaBYi40tpYI9bAJFCg9ArgK0g8xovwqCOQIXSa57l7EFqSDEYD3weO7Ofnk/TWNWcADyMx87HaWCO1sUbq61ocsvYDj6DA7A0oeFpoTEYT8RC/N0NJkjWbvgbMyvPpk4j8a4DfIlFT7UPWTuB2c/3bUE6pkDgdWa0R72oKJUkG05CJXKhQz2pk0i9AK+d8YLgPWZuAG9HKehhoL9D9RJDxcgKkir3QkWRZc1cBRxToMklgEXAuGvjjgV8A9wAnAlGPvoqj1MNXgcuBtcgyyzcmoYmZ4qSHiiRr9nwauLCAl4qgQV6LROrXUVr8L1GK4UfAFJ9VtRf4JbL+bgY2F+DezsFjRISKJIMSCu+v2MbEPuBXaODnmv/nokzrlcAYH7K2I0d0FvAz5BznC6ORhdnjD4aRpKPRbCokIsCnSPWF3kUW39nIJ5sI3AH82rxWbovA+rqWJPAqcDVa9U9g0uZ5wBfM/QEhIskSdZ9DMvm9HH92omBqIs0l/xZYgUzyMoDmpoYk8Adk+Z0PPGnuZwVK3n2yNtZY4tFXXcB/Iof7SuCVDNcMimpzfZasqglPxMEiaTJyXHONpUWQz7MZ6LYiDncA9dZxHwD/bl5/3XPdKjNYc5BT/Q6KDCwDtkKvqAWoPOzbSGRNzuH+1wNnAttDQ1Kh4cx+epPk4C20Wu5DK9HGZDTof23+fhn5Tg+hujy/ENNxuCuyPzmvDpTUfDQ04i4EmIrSIP+BouMjrPe2ogzwTOBe5BosRZZeDCjz6KsESovMBi5BtXldWd5POXJww6OTQoIoKgf7F7SiTjWvOfrqFaR3LkbO8JnAgygyMa021hjx6KsOFNW4EInMP5CdGD8ZGFtQcWdu+CPIaQxDkUgSiZDTAh6/A/hXZO390fPeGORXNaCS5c3muPuQpeinr2qBv0G+WZB6wHeBmcUgKYYSbIWoHi0GkijIegfwb8hitDEFibXLgfHIQV6EnOJ90IusKCp5vhb5WSMyXLsLmD0k7vpGBBX1L0KGwrlAhfX+ZuDvkG+3AvgEEpfLgc/SO8TUDbyAKmy/hsq80oWYyoBjh1ZS9mhDAdnbkHGQaG5qcJ51ODADVTKdisz7+5AYfBN8ReB4FBO8Cvioz/Ue9yXJnKAShWaSqBKz3ckamhuaiCK2JWgmrAF2ODdhHRdDIZZWFEJpNW/PQo6i8+D/jfwaUKG93w2HCe+ggOxSYIvnvWqUOKxHlqAtLndCL7IiaKfHNWh12ZGQDZnE3anIdHwaBTy9OAnFvB5EzuCxGc4VQbL8DuBW8/Oi9f5W5IdcgPyK3wzg4AfFJCTmHkOFLqOt995HK+1sZPlV4/pV5wEVnnhgEu19ugZlafdb56rORFKFuZFJ+Ne3RZDFFjW/cxGdSZQO6EYhlVzDKsWCs0lgCdJHZ2ICo2albELZ3S+hiXcSWk3LMFLIJ8R0F7DSukZV6ZJVNc7ug3YskYYKCeeaAXvT5wa7kR/QbT47CjitNtZYbgZ9A/LcW5HFE0EirMq8P9Y6VwmaCMPMe6VIIb9pPjcCreb+bPoqBoYBX0R+za+AxbWxRqdMLI7qJf4PGR1zkIMbQ47xstpY42YduwCUDvktbpC5PLJkVc3LZpAWA0u91SqG6XIkW4ehpbgJbS35hPlsJ1rudyN52o1k60NmwIeh/Mu3zbER5Ni9Zf4uQ/n9qHmwaUh//TOudfUU0oODAVvM/d+L9LmNE5EuO8aM03JD3IfNTQ2OjjoPRd9LgWQpijFBZudqIlrOhyMleA6wrbmp4RnoIbIOWTcV5uKOpx6vjTU6oqzaOud8YKH5+2jgOc89dOKGUjoGetSzxOEojPRlpIseQZP5AqR3pqE9uEsRkR9CisU3Cdf57y5FK6iEVEXuRRStlCrz20//JBERzk8CUgKb2eqsEutz2VSshgUlSO8sQ+KtFIm4/ciPuh1FupM9FvH3akAl0Hbxzf7S+rqWHptZFkePDihFg92GZvIGZD5uLi2Lx2/96U3Dtr5dEwU4+piGzisuanwtEuEyXJH1v+a009FyPtnzEBXmhiK4eqrD/I6gytOTzLFVpOqwwYQKFJjtQtJiIbKaO213xayiclT2/Hnr83tSZrflkN2OZsFGFGv60AxUyd62yvhN826tGD6ifQGKy0WQxdKrAtPyk55ANQT3ohVWgSyeKvP/SOT4/QCJuVJUb3fKQI9wHuBkcBuBBzCpdh8/aToKFV1KquO/3i/oGUUy81NI4ZeiJdlqnbTKDKBTtLi2jxstQbrsLlxSLiTV/3oKeBatpjI0owY7tqGJ+XOgBXwjDoeimo7ZqCbdiy1+JHWjAR2DMpXFKLU90NCG8lK3IdM74UNOJbLi5qCSsnQ+6+t+JO1HFkgZIqgtzTHPIDM6gpZzJjh+0XFoElTSuyB+AvIjutDqLebuhnwhjgKmC5Fk2A+9Vk8pkkLXoThfRYbzdQLrs44SWDPBJrgnSuCjkyaZm3GMEHCNhTLcJJhjHTqIkv9dH0m0L+j0PJ8XJH2WoM1hfvE5UHyu3tzDuADn3E6h80lhQoAah/5iB6p+vZP0ke5q3Eh3NlW5zwPnhCFbOljRjqL7i1B+qNuHnOHAWUi0nUz2/t4LwO4hkrJHN/IB+8q+noCbfe3PhrT9yNoNRd2B/XCgkEo1+dnD6sQZ4/Pn5eU2m3HLvjLVMcxGmddc+hq9jgkIhIYkC2eh0qpcEUVRknMxijwHfIDKtxZjCih9yBmLqli/g/zMXPX945j6v9CQVF/X4jzwsyi9UZPbGQGlT3IZrA7kaixE6YauNKGczyO98+fkp93Ae2hLDvV1LeEhycIm5Ag25HqiHJBANXa3U9gq1XR40lwfCNFKspBEUeILkY9VbPwJxSHvJnO997fMTy713n74AOWbuhyfM1QkWSLvFZS/uraIl2/FrUZdj38oZxTKCTXgJjzzjUdQ1KIHoSIJeohKICvqLJQQLCS6UKuzhWgLi18KoQxVNl2PovqFSuO/jYyTLjtyEzqSLLyBZnUjhen9k0R1HIvRqt0NaVMIV6OS4jEFfN4Eilq85H0jlCRZYu9+tMnr4jyePoJKrpahOoTNkDaF8A3k80wpwmOvJE1XlFCSZGEPavp0LPI9skWE3ib4UyjWtob0KQSnqscp/iw0WlC9na8/F9pacE9p2c30LpQPAr+UyG+A/6E3QaXIz1mOLLsTizQ+bYigNZ7n7kHoo+CWT9KAIhEVWXy8E5X7PuA3ANbqORI3hVAd/PQ5owv4R9SSoGtQdulyYAazArUcu4HsDInfocHfDCn6Dtya7avof/+i/qIb6cS5qA9f2gMHBUmQsolgHlpV2RD1HJqxL6KgaxVK/DWgFEKxdXM3qn24EdjdV4vPQUMS9BA1Em3vn0N222n2oOqn3Sg6fUSWn88XulBhys0EIAgGGUmQEtSsNw86mOrx9qCC7wX0IeJsDDqSICWxdi4SY4WOSuQDLaiB+y/JYCT4YVCSBCmW2ceRnppFdv1Zi4U4clRvwSTxDooO+w4soqqQlXYdhWu/1h9sQWGnu1F0++D6rgobni2NV6JS3UNyOWeO2InyUIsx7awP2m998cLSVZ9G+6O+hPI9xXrO7WgD2D3I3M9K96TDAUWSA4uso5BxMQtlUUcW4HL7UAXvYyij/CoQz+fXxh2QJDmwdNZotAHhc6i3wnQUbcgmxOSgE0XR30DRjGdRvfcuyN9XxNk4oEmy4SlYnIwaPk1D+3gnIh02EncnSQIR0oYI2Ipa12xEBG1F+1sLQoyNg4YkP1jERdGqKkMEOSTFUcVQz9bQQhMyhCEMYQhDGEKY0S/DwdrrU4LbxaQTe7v7EPKGrEiyyJmAtr3XIXO2C5mlT6IatpTtIAMBc69jUY9tp5WO/dwJFL5Zhzpu2fgMbuHLS8B651nMeaOoimkSSuCtRjVzJWZMPoJ/f6R2ZMJvxFNXngmBM5IWQWegnPyJpG6KmoHKbn+NIr4ttbHGASUKbaO5E2199NtK04V2SXwfVY46x3wFtxb9ZlTRaqMM+C7wF0iCXIRIiqIg76w010sgR/he4Me1scbWIOMTqBrGIqjOXOAU/HetjUAxs7sIRx8gu6Qr4vMzDJULLya1Z0Qkzd/4vB5Jc7zf9aJICt2ICv0j1timRTYlS+PQjHM8wFZUvFhvLvocbjuBGWifTqCbKBL2ohDOE6j27mXcvnKTUXOqfLfHeQXtM3oSbZ1xvoMpinq2Bkqr9CnurEH+Iop7gUIlN2Gq/81r96CuWleY/y9Bq+5NS44736fXjdsGYBjaj9MGqXrMuvYoFGtLoI3Ee73HBsA21NRiK5qcI9EEuwV32/4Eeuun/iKB0uS/wI1knIJySzVIb50AbOpLLQRdSVEkZ50KnYcNAV3NTQ3OBXahVPZGc0wNbmvnSlQz96A55qPmAZ5DhsYjyBDpWXnmdxUqt3rCHLcK7VP9Oqb7YhZwLNB4c1NDp7nf5WirC6gX6vg8EeQgjizeTmRMNaFNAc6Y1gY5SVDDoRrJblAs6wH8v+HkLdQO9Cg0AU5Ce43K0G6E4xFBU0n9fqTDkDV1KbDKDH4FEq9Xk1q+dTgqw5oAzK+NNSZyME7acUVelMJ/KXAStwctBNydkQ1JTqZzByr9TVmiVkfftWiplyAyvObvEeZnGyqvmoIi05OQdfg8EoczULF8mRnIjbjNEYejbSir0Tb6rGCtwENRc0WQCG31OXwG+g4l2zgopX9p+nJSi/93BPlQUJJG4eZePsA00UuDd9Aqq0B+ine2lKN9qNebY78J/BDX0hpjzn8JbhfKn6AtMBWo2d/laNJcDLwQ0NQvRQZCFE2gsUivOpOvGWVWvfgsri7OBhFkbE2qjTU6kfWZyIUBTYpekz0XkobhWj52R0c/dOBaec7nbMeuHemjdeb/FahdW60hpRzN8BPM+5vQlhCnYH8R6hJ8KFLEo8k8aRwchuoOnNY4I3FXURJlVVsDnCcoIsgouQ63hel43IqmtShZ2CeCkhS3BtrpYJzpnCU+n3Owi9TGu3sxG4etAZtAau+d83EnRiXuJJiIVkQQksqQReWHleg7KfywGpnPtpFVihzYKX1cM50xsh1ZwrsD3HdgkpzukSOR6BtJ+q0oh+CKuFbzOVvkxUldiUlSiUwip9hR4kcjUeeHEQTvNtJt7se5VhJNjmeAf8I0z/DBSuCHnrBQBSp2mRJw3Bx0os5mC5CvFsiNCErS+0gXjUMkTMXTVd5SxsfhrrQtSOl7awn66nbSbR2zC+kLLyJoJgb9XqKtSJe9i1ZFAq3A7Vmcw3v9TEgiXfso7ji3m+ultBzoC9mQ9DqyaCpRN+NVtbFGb9R7PGpg7uD3/Xz4VkRuJZLdX7UG0m65liS1G30mdCBFvS3d4OQ5OpJEk/S1XOOXQUnqQMtzphnEy8z/T1sPVoZMZqdl53tIlmeLCO6XJ45DBsVw3JDKKDQRKtHqfobB15I6K/QZcbBmwaO4HSInoO0b30VlUjOQ3piLK+oeo++OkunuaQduC+wj0caxGmTRXY2K3pcjv+qARzabp1qQgr0TzeYaFOJpR8TYxsEG1BehP7IeJCruR4WNY1Bo6BxzvhpkxrYhog7oVQQBY3fWalqBHEAn3uX0EHcISqBNw7PJvIqCJBubkJm6B02mWlTrXY7M9gVotQ50zipIWiMnBF5JJuwTR47lOjS7Y0gExVHc7iE87ZWNzupEu75fxYp4G3QgR3Idsnz2mtfjaDW2oKjEdLRiN6Lg7go83Ut8sAv1LK/CfAdUwMddg5xk58sWvehG6Yc/mWfbYr2+EmV8nTHJGf8Pop7ueMVAuUIAAAAldEVYdGRhdGU6Y3JlYXRlADIwMTUtMDYtMDJUMDU6MDM6MTUtMDQ6MDAaPdUcAAAAJXRFWHRkYXRlOm1vZGlmeQAyMDE1LTA2LTAyVDA1OjAzOjE1LTA0OjAwa2BtoAAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAAAASUVORK5CYII='>
                    <h2>Error</h2>
                        <p>$msg</p><br/>";

            switch($number){
                case 1:
                    echo "<b>Note</b>: Routes begin always with '/' character.";
                    break;
                default: break;
            }

            echo " <h2>Trace:</h2>";
            throw new \Exception($msg);
        }
        return false;
    }
}

/**
 * Class Route
 * Formed by a string with the path and one callback function
 */
class Route{
    public  $route;
    public  $function;

    /**
     * @param string $routeKey like /books/{id}/edit
     * @param callable $func Function
     */
    public function __construct($routeKey = '', callable $func){
        $this->route = $routeKey;
        $this->function = $func;
    }
}

/**
 * Class Request
 * Manage request params
 */
class Request{
    private $get;
    private $post;
    private $files;
    private $server;
    private $cookie;
    private $method;
    private $requested_uri;
    private $body = null;

    public function __construct(array $GET = array(),array $POST = array(),array $FILES = array(),array $SERVER = array(),array $COOKIE = array()){
        $this->get = $GET;
        $this->post = $POST;
        $this->files = $FILES;
        $this->server = $SERVER;
        $this->cookie = $COOKIE;
        $this->method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        $this->requested_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/' ;
    }

    public static function createFromGlobals(){
        return new Request($_GET,$_POST,$_FILES,$_SERVER,$_COOKIE);
    }

    /**
     * Get query param passed by URL
     * @param $key
     * @return value or false
     */
    public function get($key){
        return isset($this->get[$key]) ? $this->get[$key] : false;
    }

    /**
     * Get post variables
     * @param $key
     * @return value or false
     */
    public function post($key){
        return isset($this->post[$key]) ? $this->post[$key] : false;
    }

    public function server($key){
        return isset($this->server[$key]) ? $this->server[$key] : false;
    }

    public function cookie($key){
        return isset($this->cookie[$key]) ? $this->cookie[$key] : false;
    }

    /**
     * Returns the Request Body content from POST,PUT
     * For more info see: http://php.net/manual/en/wrappers.php.php
     */
    public function getBody(){
        if ($this->body == null)
            $this->body = file_get_contents('php://input');
        return $this->body;
    }

    /**
     * Get headers received
     * @param $key
     * @return value or false
     */
    public function header($key){
        return isset($_SERVER['HTTP_'.strtoupper($key)]) ? $_SERVER['HTTP_'.strtoupper($key)] : false;
    }

    public function getMethod(){
        return $this->method;
    }

    public function  getRequestedUri(){
        return $this->requested_uri;
    }
}

/**
 * Class View
 * Load a view File with access to data and Framework
 */
class View
{
    protected $data;
    protected $framework;
    protected $src;

    /**
     * @param $src Source file to load
     * @param array $vars Associative key , values
     * @param null $framework isntance
     */
    public function __construct($src, array $vars = array(), App $framework = null){
        $this->data = $vars;
        $this->framework = $framework;
        $this->src = $src;
    }

    /**
     * Renders a view
     * @throws Exception if View not found
     */
    public  function load(){
        $app = $this->framework;
        $data = $this->data; //deprecated, vars are passed directly since version 0.0.4
        extract($this->data,EXTR_OVERWRITE);//set global all variables to the view

        if (file_exists($this->src))
            include_once($this->src); //scoped to this class
        else{
            if($this->framework ){
                if($app->getEnvironment() == ENV_DEV)
                    return $app->error("View filename '{$this->src}' NOT found in '". VIEWS_ROUTE."'.<br/>
                     Maybe you need to change the App::APP_DIR or App::VIEW_DIR Constant to your current folder structure.");
                else
                    return $app->error();
            }
        }
    }
}
