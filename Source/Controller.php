<?php
class ControllerMap
{
    public $URL;
    public $Controller;
    public $Parameters;

    public function __construct($URL,$controller,$parameters)
    {
        $this->URL 			= $URL;
        $this->Controller 	= $controller;
        $this->Parameters	= $parameters;
    }
    public function MatchURL($url)
    {
        if ( $this->URL == "/" )
        {
            return $url == "/";
        }
        $quotedURL 		= preg_quote($this->URL,"/");
        $patternMatch 	= '/^'.$quotedURL."[0-9a-zA-Z\/]*/";
        $matched 		= preg_match($patternMatch,$url);
        //echo "{$patternMatch} = {$url} ? {$matched} <br />\n";
        return $matched == 1;
    }
}

DEFINE('REQUEST_GET',1);

DEFINE('REQUEST_POST',2);

function GetRequestMethodMask()
{
    $requestMethod = 0;
    $supportedRequestMethods = array('POST'=>REQUEST_POST,'GET'=>REQUEST_GET);
    return $supportedRequestMethods;
}

function CheckRequestMethod()
{
    $requestMethod = 0;
    foreach (GetRequestMethodMask() as $supportedRequestMethod => $value)
    {
        $ifRequestMethod = ($supportedRequestMethod == $_SERVER['REQUEST_METHOD']);
        if ($ifRequestMethod) {
            $requestMethod |= $value;
        } else {
            $requestMethod &= ~$value;
        }
    }
    return $requestMethod;
}

function TrimForwardSlashes($value) { return trim($value,'/'); }

function PathCombine($paths)
{
    $args = func_get_args();
    $pathItems = array_map('TrimForwardSlashes',$args);
    $newPath = '/'.trim('/'.join('/',$pathItems),'/');
    return $newPath;
}

class Route
{
    protected $httpMethods;
    protected $url;
    protected $fn;
    protected $controller;
    protected $parameters;

    public function __construct($ctrlr,$methods,$URL,$fn,$parameters)
    {
        $this->url			= $URL;
        $this->httpMethods 	= $methods;
        $this->fn 			= $fn;
        $this->controller	= $ctrlr;
        $this->parameters	= $parameters;
    }

    public function Run($url,$requestMethods)
    {
        $expr			= '@'.$this->url.'@';
        $matches 		= array();
        $urlMatch 		= preg_match('#'.$this->url.'#',$url,$matches);
        $requestMethod 	= (( $this->httpMethods & $requestMethods) == $requestMethods);
        if ($urlMatch && $requestMethod)
        {
            $fn = $this->fn;
            $fn($this->controller,$this,$matches,$this->controller->GetModels());
        }
    }
}

class Controller
{
    protected $routes;
    protected $rootURL;
    protected $data;
    protected $config;
    protected $models;

    public function __construct($rootURL,$data =array(),$config = array())
    {
        $this->routes 	= array();
        $this->rootURL 	= $rootURL;
        $this->data 	= $data;
        $this->config 	= $config;
        $this->models	= array();
        $this->errors	= new ViewData(array());
    }	
    public function Add($httpMethod,$urlExpr,$fn)
    {
        $this->routes[] = new Route($this,$httpMethod,PathCombine($this->rootURL,$urlExpr),$fn,array());
    }
    public function AddModel($model)
    {
        if (!array_key_exists($model,$this->models))
        {
            $this->models[$model] = LoadModel(Common::LocalDB(),$model);
        }
    }
    public function GetModel($category,$model)
    {
        $this->AddModel($category,$model);
        return $this->models[$model];
    }
    public function GetModels()
    {
        return $this->models;
    }
    public function Initiate($controllerFileName)
    {
        $parameters = array_key_exists('parameters',$this->data)?$this->data->parameters:array();
        $data       = $this->data;
        $config     = $this->config;
        $errors     = $this->errors;
        require_once(APP_FOLDER ."Controllers/{$controllerFileName}.php");
        return $this;
    }
    /**
     *
     *	@param $url URL of the request
     * 	@param $requestmethods The request type, GET or POST
     */
    public function Run($url,$requestmethods)
    {
        ob_start();
        foreach($this->routes as $route)
        {
            $route->Run($url,$requestmethods);
        }
        return ob_get_clean();
    }
    public function CreateView()
    {
        return new View($this->config->theme,$this->data,$this->config);
    }
    public function CreatePageView()
    {
        return new PageView($this->config->theme,$this->data,$this->config);
    }
    public function ShowView($template)
    {
        $view = $this->CreateView();
        $view->ShowView($template);
    }
    public function ShowPageView($template)
    {		
        $view = $this->CreatePageView();
        echo $view->Show($template);
    }	
    public function GetData()
    {
        return $this->data;
    }
    public function GetConfig()
    {
        return $this->config;
    }
    public function GetDefaults()
    {
        return $this->defaults;
    }
    public function AddData($key,$value)
    {		
        $this->data->Add($key,$value);
    }
}

$controllerMap = array
(
    new ControllerMap('/characters',"characters",array()),
    new ControllerMap('/simm',"simm",array()),
    new ControllerMap('/stories',"story",array()),
    new ControllerMap('/users',"users",array()),
    new ControllerMap('/',"index",array())
);

function SearchControllerMaps(array $list, $url,ControllerMap $default)
{
    $found = $default;
    foreach ($list as $ctlr)
    {
        if($ctlr->MatchURL($url))
        {
            $found = $ctlr;
            return $found;
        }
    }
    return $found;
}

require_once(SOURCE_FOLDER."Model.php");

function Debug($data)
{	
	echo "<pre>".print_r($data,true),"</pre>";
}
/*
function LoadModel($db,$category,$name)
{
	//Debug(func_get_args());
	$name= "models\\$name";
	if (!class_exists($name)) {
		$file = APP_FOLDER . "Models/{$category}.php";
		require_once($file);
	}
	return new $name($db);
}
*/
class Common
{
    protected static $db;
    public static function LocalDB()
    {	
        if (Common::$db == null)
            Common::$db = new PDO(LOCAL_DB_DSN,LOCAL_DB_USERNAME,LOCAL_DB_PASSWORD);
        return Common::$db;
    }
}

class Users
{

    public static function GetGroups()
    {
        $groups = (isset($_SESSION['user']->groups)?$_SESSION['user']->groups:array());
        return $groups;
    }	
    public static function LoggedIn()
    {
        return isset($_SESSION) && isset($_SESSION['user']) && isset($_SESSION['user']->Id);
    }	
}