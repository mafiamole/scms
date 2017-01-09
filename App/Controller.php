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
		if ( $this->URL == "/" ) {
			return $url == "/";
		}
		$quotedURL = preg_quote($this->URL,"/");
		$patternMatch = '/^'.$quotedURL."[0-9a-zA-Z\/]*/";
		$matched = preg_match($patternMatch,$url);
		//echo "{$patternMatch} = {$url} ? {$matched} <br />\n";
		return $matched == 1;
	}
}
$controllerMap = array
(
	new ControllerMap('/characters',"characters",array()),
	new ControllerMap('/simm',"simm",array()),
	new ControllerMap('/quests',"quests",array()),
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
require_once(APP_FOLDER."Model.php");
function Debug($data) {	
	echo "<pre>".print_r($data,true),"</pre>";
}
function LoadModel($db,$category,$name) {
	$name= "models\\$name";
	if (!class_exists($name)) {
		$file = APP_FOLDER . "Models/{$category}.php";
		require_once($file);
	}
	return new $name($db,"app");
}

class Common {
	protected static $db;
	public static function LocalDB() {		
		if (Common::$db == null)
			Common::$db = new PDO(LOCAL_DB_DSN,LOCAL_DB_USERNAME,LOCAL_DB_PASSWORD);
		return Common::$db;
	}
	
	public static function SetUserData($data)
	{

	}	
}
function GetGroups()
{
	$groups = (isset($_SESSION['user']->groups)?$_SESSION['user']->groups:array());
	return $groups;
}

class System
{
	public static function LoggedIn()
	{
		return isset($_SESSION) && isset($_SESSION['user']) && isset($_SESSION['user']->Id);
	}
}