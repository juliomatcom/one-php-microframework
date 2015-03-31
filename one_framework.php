<?php
/**
 * One PHP MVC Micro Framework
 * Version 0.1.6
 * @author Julio Cesar Martin
 * juliomatcom@yandex.com
 * Contribute to the project in Github
 * http://oneframework.net/
 *
 * Controllers must be in APP_DIR/controllers
 * Views must be in APP_DIR/views
 * Translations must be in APP_DIR/translations
 * Assets must be in APP_DIR/assets/
 */
class OneFramework{
    //instances vars and predefined configs
    protected $request;
    protected $db;
    protected $routes = array();

    //set this value to True if you want to get access to translations by URL
    protected $translate = false;
    //here the value of the locale requested by url (segment 1)
    protected $locale = null;
    protected $locales = ['es','en','fr'];//First value is the default locale
    protected $translations = array();

    protected $prod = false;

    /**
     * Initialize Framework Core
     * @param bool $prod Enviroment, set to false for Enable Debugging
     */
    public function __construct($prod = false){
        $this->setEnviroment($prod);
        $this->defineConstants();
        $this->buildRequest();
        $this->loadTrans();
    }

    /**
     * Start listen for requests
     */
    public function listen(){
        $slugs = array();

        $run = ($this->request->type != 'GET') ? $this->traverseRoutes($this->request->type,$slugs) : false;
        $run = $run ? $run : $this->traverseRoutes('GET',$slugs);

        if(!$run && (!isset($this->routes['respond']) || empty($this->routes['respond']))){
            $this->error('Route not found for request method: '.$this->request->type, 1 );

        }
        else if(!$run){ //respond for all request;
            if($this->translate) $this->locale = $this->getSegment(0);
            $callback = $this->routes['respond']->function;
            $callback();
        }
    }

    /**
     * Change eniroment to prod or not
     * @param $prod bool
     */
    public function  setEnviroment($prod = false){
        $this->prod = $prod;
        if($prod){
            // No show any errors
            error_reporting(0);
        }
    }

    /**
     * Connect if needed, retrieve the database connection
     * @return mysqli
     */
    public function getDB(){
        $this->db = $this->db ? $this->db : new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
        if ($this->db->connect_errno) {
            $this->error("Error connecting to database: ".DB_DATABASE."<br/> Info:". $this->db->connect_error);
        }
        return $this->db;
    }

    private function defineConstants(){
        define('APP_DIR',__DIR__);
        define('CONTROLLERS_ROUTE',APP_DIR.'/controllers/');
        define('VIEWS_ROUTE',APP_DIR.'/views/');
        define('TRANSLATION_DIR',APP_DIR.'/translations/');
        define('DB_HOST','127.0.0.1');
        define('DB_USER','root');
        define('DB_PASSWORD','');
        define('DB_DATABASE','');
        define('APP_NAME','');
    }

    private function buildRequest(){
        $this->request = new stdClass();
        $this->request->query = $_GET;
        $this->request->request = $_POST;
        $this->request->server = $_SERVER;
        $this->request->cookie = $_COOKIE;
        $this->request->type = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
    }

    /**
     * Create a route to the app
     * @param $uri
     * @return string new URL with locale if needed
     */
    public function getRoute($uri){
        return $this->translate ? ((APP_NAME != '') ? ('/'.APP_NAME.'/'.$this->locale.$uri) : ('/'.$this->locale.$uri) ):
            ((APP_NAME != '') ?('/'.APP_NAME.$uri) : $uri);
    }

    /**
     * Process the request and Return a Response
     * @param $uri string for the Route example: /book/{number}/edit
     * @param $callback function
     */
    public function get($uri,callable $callback){
        $routeKey = $this->translate ? ('/{_locale}'.$uri) : $uri;
        //save route and function
        $this->routes['GET'][] = $this->createRoute($routeKey,$callback);
    }

    /**
     * Process a POST Request
     * @param $uri string
     * @param $callback function
     */
    public function post($uri,callable $callback){
        $routeKey = $this->translate ? ('/{_locale}'.$uri) : $uri;
        $this->routes['POST'][] = $this->createRoute($routeKey,$callback);
    }

    /**
     * Process a PUT Request
     * @param $uri string
     * @param $callback function
     */
    public function put($uri,callable $callback){
        $routeKey = $this->translate ? ('/{_locale}'.$uri) : $uri;
        $this->routes['PUT'][] = $this->createRoute($routeKey,$callback);
    }

    /**
     * Process a DELETE Request
     * @param $uri string
     * @param $callback function
     */
    public function delete($uri,callable $callback){
        $routeKey = $this->translate ? ('/{_locale}'.$uri) : $uri;
        $this->routes['DELETE'][] = $this->createRoute($routeKey,$callback);
    }

    /**
     * Process all Request
     * @param $uri string
     * @param $callback function
     */
    public function respond(callable $callback){
        $this->routes['respond'] = $this->createRoute('',$callback);
    }

    /**
     * If locale is set return locale from values in array $locales
     * @return bool|null
     */
    public function getLocale(){
        return $this->locale ? $this->locale : false;
    }

    /**
     * Get current enviroment
     * @return bool True if Production is ON
     */
    public function getEnviroment(){
        return $this->prod;
    }

    /**
     * Get request Object
     * @return Request class
     */
    public function getRequest(){
        return $this->request;
    }

    public function setStatusCode($status = 200){
        if($status != 200){
            if (!function_exists('http_response_code'))//PHP < 5.4
            {//send header
                header('X-PHP-Response-Code: '.$status, true, $status);
                return $status;
            }
            else return http_response_code($status);
        }
    }

    /**
     * Translate a string
     * @param string $key
     * @param bool $lang Language
     * @return string|exception Error
     */
    public function trans($key,$lang = false){
        if($this->translate){
            if(!$lang) $lang= $this->locale;
            return isset($this->translations[$lang][$key]) ? $this->translations[$lang][$key] : 'translation_'.$key;
        }
        else return $this->error(
            'You can not use translation because is disabled in the Framework.<br/>
             Set the value of \'translate\' property to true.');
    }

    /**
     * Load translations from disk
     */
    private  function loadTrans(){
        if($this->translate){//include translations
            foreach($this->locales as $locale){
                $filename = 'trans.'.$locale.'.txt';

                if(file_exists(TRANSLATION_DIR.$filename)){
                    $text = file_get_contents(TRANSLATION_DIR.$filename);
                    $lines = explode(PHP_EOL,$text);
                    $arr = array();

                    foreach($lines as $line){//add pairs
                        $par = explode(':',$line);
                        if(!empty($par[0]) && !empty($par[1]))
                            $arr[utf8_decode($par[0])] = trim($par[1]); //save
                    }

                    $this->translations[$locale] = $arr;
                }
            }
        }
    }

    /**
     * Returns al the translations loaded
     * @return array
     */
    public function getTranslations(){
        return $this->translations;
    }

    public function Redirect($href){
        echo header('Location: '.$href);
        exit;
    }


    /**
     * Return a new HTTP response.
     * @param string $view_filename Source to the file
     * @param array $vars Data to pass to the View
     * @param array $headers Http Headers
     */

    /**
     * @param $view_filename Source to the file
     * @param array $vars Data to pass to the View
     * @param int $status Set the response status code.
     * @param array $headers Set response headers.
     */
    public function Response($view_filename,array $vars = array(),$status = 200, array $headers=array()){
        $this->setStatusCode($status);

        if(count($headers)){//add extra headers
            foreach($headers as $key=>$header){
                header($key.': '.$header);
            }
        }
        //pass to the view
        $view = new View(VIEWS_ROUTE.$view_filename,$vars,$this);
        $view->load();
        exit;
    }

    /**
     * Return a new Json Object Response
     * @param array $data Array to encode
     */
    public function JsonResponse(array $data = array()){
        header('Content-Type: application/json');//set headers
        echo json_encode($data);
        exit;
    }


    /*REGION PRIVATE FUNCTIONS FOR THE APP CORE*/
    /**
     * Traverse the routes and match the request
     * @param string $method Request Method
     * @param $slugs Save {vars}
     * @return bool
     */

    private function traverseRoutes($method = 'GET',&$slugs){
        if(isset($this->routes[$method])){
            foreach($this->routes[$method] as $route)
                if($func = $this->processUri($route,$slugs)){
                    //call callback function with params in slugs
                    call_user_func_array($func,$slugs);
                    return true;
                }
        }
        return false;
    }

    private function processUri($route,&$slugs = array()){
        $url = isset($this->request->server['REQUEST_URI']) ? $this->request->server['REQUEST_URI'] : '/' ;
        $uri = parse_url($url, PHP_URL_PATH);
        $func = $this->matchUriWithRoute($uri,$route,$slugs);
        return $func ? $func : false;
    }

    private function matchUriWithRoute($uri,$route,&$slugs){
        $uri_segments = preg_split('/[\/]+/',$uri,null,PREG_SPLIT_NO_EMPTY);
        //redirect if no locale is set
        if(count($uri_segments) == 0 && $this->translate){
            APP_NAME!= '' ?   $this->redirect("/".APP_NAME."/{$this->locales[0]}/") : $this->redirect("/{$this->locales[0]}/");
        }
        $route_segments = preg_split('/[\/]+/',$route->route,null,PREG_SPLIT_NO_EMPTY);

        if($this->compareSegments($uri_segments,$route_segments,$slugs)){
            //route matched
            if($this->translate) $this->locale = $this->getSegment(0); //save locale
            return $route->function; //Object route
        }
        return false;
    }

    /**
     * Create an object Route
     * @param $routeKey Unique key
     * @param callable $function executable
     * @return stdClass
     */
    private function createRoute($routeKey,callable $function){
        $route = new stdClass();
        $route->route = $routeKey;
        $route->function = $function;
        return $route;
    }

    /**  Match 2 uris
     * @param $uri_segments
     * @param $route_segments
     * @return bool
     */
    private function CompareSegments($uri_segments,$route_segments,&$slugs){

        if(count($uri_segments) != count($route_segments)) return false;

        foreach($uri_segments as $segment_index=>$segment){

            $segment_route = $route_segments[$segment_index];
            //different segments must be an {slug}
            if(preg_match('/^{[^\/]*}$/',$segment_route) && $segment_route!='{_locale}')
                $slugs[] = $segment;//save slug key => value
            else if($segment_route != $segment && preg_match('/^{[^\/]*}$/',$segment_route) != 1) return false;

        }
        //match with every segment
        return true;
    }

    private  function getSegment($segment_number){
        $uri = isset($this->request->server['REQUEST_URI']) ? $this->request->server['REQUEST_URI'] : '/' ;
        $uri_segments = preg_split('/[\/]+/',$uri,null,PREG_SPLIT_NO_EMPTY);

        return isset($uri_segments[$segment_number]) ? $uri_segments[$segment_number] : false;
    }

    /**
     * Show framework's errors
     * @param string $msg
     * @param int $number
     */
    private function error($msg='',$number = 0){
        if($this->prod){
            //do something here
            return false;
        }
        else{
            $frw_msg =
                "<h1>One Framework: Error</h1>
             <p>$msg</p><br/>";

            switch($number){
                case 1:
                    $frw_msg = $frw_msg."<b>Note</b>: Routes begin always with '/' character. If app add a /{lang}/ path is because is enabled the framework's translations.";
                    break;
                default: break;
            }

            $frw_msg = $frw_msg." <h2>Trace:</h2>";
            echo $frw_msg;
            throw new Exception();
        }
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
    public function __construct($src,array $vars,$framework = null){
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

        if(file_exists($this->src))
            include_once($this->src); //scoped to this class
        else{
            if($this->framework && !$this->framework->getEnviroment())
                throw new Exception("ONE Framework: View filename: {$this->src} NOT found in ". VIEWS_ROUTE);
        }
    }
}