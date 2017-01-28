<?php
session_start();
require_once("config.php");


class AppError
{
    protected $header;
    protected $body;
    protected $footer;
    protected $date;
    public function __construct()
    {
        $args = func_get_args();
        if (count($args) == 1 && $args[0] instanceof Exception)
        {
            $this->fromException($args[0]);
        }
        else if (count($args) == 3)
        {
            $this->fromParams($args[0],$args[1],$args[2]);
        }
        else if (count($args) == 4)
        {
            $this->fromErrorDetails($args[0],$args[1],$args[2],$args[3]);
        }
        else
        {
            $this->header = "Unknown error type";
            $this->footer = "--";
            $this->body = "Invalid error class arguments provided";
            $this->date = date('N');            
        }
    }
    protected function fromException(Exception $ex)
    {
        $this->header = get_class($ex). " was thrown.";
        $this->footer = "File: ".$ex->getFile() . " Line: ". $ex->getLine();
        $this->body = $ex->getMessage();
        $this->body .= "<br />\n<pre>".$ex->getTraceAsString()."</pre>";
        $this->date = date('N');       
    }    
    protected function fromParams($header,$body,$footer)
    {
        $this->header = $header;
        $this->footer = $footer;
        $this->body = $body;
        $this->date = date('N');
    }

    protected function fromErrorDetails($errno, $errstr, $errfile, $errline)
    {
        $this->body     = "[$errno] $errstr";
        $this->footer   = "File: {$errfile}, Line: {$errline}";
        $this->date     = date('N');
        switch ($errno)
        {
            case E_USER_ERROR:
                $this->header = "ERROR";
                break;
            case E_USER_WARNING:
                $this->header = "WARNING";
                break;
            case E_USER_NOTICE:
                $this->header = "NOTICED";
                break;
            default:
                $this->header = "UNKNOWN ERROR";
                break;
        }
    }
    public function getHeader() { return $this->header; }
    public function getBody() { return $this->body; }
    public function getFooter() { return $this->footer; }
    public function getDate() { return $this->date; }
}
function SendErrorEmail(AppError $err)
{
    $template=
        '
        An error has occurred on the website.<br />
        We appologies for the inconvenance. Below are the technical details of the error.
        You may forward this email to johnmorgan@14thfleet.com if you need any assistances or to
        help us out with fixing this for our next release!<br />
        <hr />
        <h2>Technical details</h2>
        <h3>%s</h3>
        <p>
        %s
        </p>
        %s
        %s
        <hr />        
        ';
    $message = sprintf($template,$err->getHeader(),$err->getBody(),$err->getFooter(),$err->getDate());
    mb_send_mail(ADMIN_EMAIL,"An error has occured",$message);
}
function ShowError(AppError $err)
{
    printf('
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">	
    <title>Error</title>	
</head>
<body>   
<main class="container"  role="main">
    <header>
    </header>
    ');
    if (defined("DEBUG"))
    {
        $template = 
        '
            <div class="panel panel-danger">
                <div class="panel-heading">%s</div>
                <div class="panel-body">
                %s
                <br /><a href="/" class="btn  btn-primary btn-lg">Back to Home</a>
                </div>
                <div class="panel-footer">%s</div>
            </div>
        ';
        printf($template,$err->getHeader(),$err->getBody(),$err->getFooter());        
    }
    else
    {
        $template = 
        '
            <div class="panel panel-danger">
                <div class="panel-heading">%s</div>
                <div class="panel-body">
                %s
                <br /><a href="/" class="btn  btn-primary btn-lg">Back to Home</a>
                </div>
                <div class="panel-footer">%s</div>
            </div>
        ';        
        printf($template,"Error","We are sorry, An unrecoverable error has occured. An administrator has been contacted regarding this error.","");
    }
printf('
    </main>
		<footer class="container text-center">
			<a href="">&copy; Starfleet Strategic Response Fleet 2016. All rights reserved.</a><br />
			Version: 0.0.0.1 (Alpha)
		</footer>
        
    <link href="/Resources/bootstrap-3.3.7-dist/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
		<script src="/Resources/js/jquery-3.1.1.min.js" type="text/javascript"></script>
		<script src="/Resources/bootstrap-3.3.7-dist/js/bootstrap.min.js" type="text/javascript"></script>
	</body>
</html>
');    

}
function ExceptionHandeler($ex)
{
    $err = new AppError($ex);
    ShowError($err);
    return true;
}
function ErrorHandeler($errno, $errstr, $errfile, $errline)
{
    $err = new AppError($errno, $errstr, $errfile, $errline);
	ShowError($err);
    /* Don't execute PHP internal error handler */
    return true;
	
}
$old_exception_handler = set_exception_handler("ExceptionHandeler");
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