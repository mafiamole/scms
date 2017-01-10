<?php
session_start();
require_once("config.php");

function ErrorHandeler($errno, $errstr, $errfile, $errline)
{
	$template = 
	'
		<div class="panel panel-default">
			<div class="panel-heading">%s</div>
			<div class="panel-body">
			%s
			</div>
			<div class="panel-footer">%s</div>
		</div>
	';
    switch ($errno) {
    case E_USER_ERROR:
		printf(
			$template,
			"Error",
			"[{$errno}] $errstr <br />Fatal error on line $errline in file {$errfile} , PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />Aborting...<br />",
			"File: {$errfile}, Line: {$errline}"
		);
        exit(1);
        break;

    case E_USER_WARNING:
		printf(
			$template,
			"WARNING",
			"[$errno] $errstr",
			"File: {$errfile}, Line: {$errline}"
			);
        break;

    case E_USER_NOTICE:
		printf(
			$template,
			"NOTICE",
			"[$errno] $errstr",
			"File: {$errfile}, Line: {$errline}"
			);		
        break;

    default:
		printf(
			$template,
			"Unknown error type:",
			"[$errno] $errstr",
			"File: {$errfile}, Line: {$errline}"
			);		
        break;
    }

    /* Don't execute PHP internal error handler */
    return true;
	
}
$old_error_handler = set_error_handler("ErrorHandeler");

$defaults 						= array();
$defaults['site_title'] 		= 'Site Title';
$defaults['page_title'] 		= 'Page Title';
$defaults['site_slogan'] 		= 'Site Slogan goes here';
$defaults['html_title_format'] 	= '%s - %s';
$defaults['page_content'] 		= 'Page content not found';

$data = array();
$config = array();

require_once(APP_FOLDER . "app.config.php");
require_once(APP_FOLDER . "view.php");
require_once(APP_FOLDER . "Controller.php");
$theme = "Default";

$data['pageController'] = SearchControllerMaps(
							$controllerMap,
							$_SERVER['REQUEST_URI'],
							new ControllerMap('/',"content",array())
						);

function SetLoggedOutGroups()
{
	if ( !isset($_SESSION['user']) || !isset($_SESSION['user']->groups)) {
		$userModel = LoadModel(Common::LocalDB(),"Users","UsersModel");
		$groups = $userModel->GetLoggedOutGroups();
        $_SESSION['user']= new stdClass();
		$_SESSION['user']->groups = $groups;
	}
}
function GetURIS($path) {
	$getParamsStart = strrpos($path,"?");
	if ($getParamsStart > 0 ) {
		$pathWOGet = substr($_SERVER['REQUEST_URI'],0,$getParamsStart);
	} else {
		$pathWOGet = $path;
	}
	$output = array();
	foreach (explode("/", $pathWOGet) as $uri)
	{
		if ( $uri && strlen($uri) > 0 ) {
			$output[] = $uri;
		}
	}
	return $output;
}


SetLoggedOutGroups();
$parameters = GetURIS($_SERVER['REQUEST_URI']);
$data['parameters'] = $parameters;
$data['TopNavigation'] = array
(
    array('ContentTitle'=>'Home','URL'=>'/','UserGroups'=>array('Id'=>1,2,3)),
    array('ContentTitle'=>'Simm','URL'=>'/simm','UserGroups'=>array('Id'=>1,2,3)),
    array('ContentTitle'=>'Stories','URL'=>'/quests','UserGroups'=>array('Id'=>1,2,3)),
    array('ContentTitle'=>'Login','URL'=>'/users/login','UserGroups'=>array('Id'=>2,3)),
    array('ContentTitle'=>'Register','URL'=>'/users/register','UserGroups'=>array('Id'=>2,3)),
    array('ContentTitle'=>'Logout','URL'=>'/users/logout','UserGroups'=>array('Id'=>1))
);
foreach ($data['TopNavigation'] as $key => $tn)
{
    $regex = "#^".preg_quote($tn->URL)."#";
    if (preg_match($regex,$_SERVER['REQUEST_URI']) !== false )
    {
        $data['TopNavigation'][$key]['Active'] = true;
    }
}

$page = new PageView($theme,$defaults,$data,$config);

echo $page->show('page.tpl.php');