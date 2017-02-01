<?php
require(APP_FOLDER . "/ThirdParty/password.php");
require(APP_FOLDER . "/Helpers/Validator.php");

function Login() {
	$sanitizer = new Sanitize($_POST);
	$email = $sanitizer->CheckEmail('Email');
	$db = Common::LocalDB();
	$model = LoadModel($db,'Users','UsersModel');
	$user = $model->FindExistingByEmail($email);
    if (!$user) {
        return array('Email'=>"User not found");
    }
	$validPassword =  password_verify($_POST['Password'],$user->Password);
	if ( $validPassword ) 
	{
		// Set up session
		$_SESSION['user'] = array();
		$_SESSION['user'] = $user;
		$setLastLogin = array
		(
			'Id' 		=> $user->Id,
			'LastLogin' => date('c')
		);
		$model->Edit($setLastLogin);
		header('location:/users/loggedIn');
		return true;
	}
	else
	{
		
		$errors = $sanitizer->GetErrors();
		$errors['password'] = "Incorrect password";
		return $errors;
	}
}

$controller = new Controller('/users',$this->data,$this->config,$this->theme);

$controller->Add
(
	REQUEST_POST,
	'/register',
	function($controller,$route,$parameters,$models)
	{
        if ( !empty($_POST) )
        {
			$sanitizer = new Sanitize($_POST);
			$this->data['PostData']['Email'] 	= $sanitizer->CheckEmail('email');
			$this->data['PostData']['Password'] = $sanitizer->CheckRegPassword('password','confirmPassword');
			if ( !$this->data['PostData']['Email'] ) {
				$postErrors['Email'] = "Invalid email address entered";
			}
			if ( !$this->data['PostData']['Password'] ) {
				$postErrors['Password'] = "Invalid password address entered";
			}			
			$model 			= LoadModel($db,'Users','UsersModel');
            $InGroupsModel  = LoadModel($db,'Users','UserInGroupsModel');
            //$groupsModel    = LoadModel($db,'Users',"UserGroupsModel");
            //$groupsModel->get(2);// TODO: make dynamic like

			$postErrors = $sanitizer->GetErrors();
            
			if ( $this->errors->Count('Post') == 0)
			{
				$user = $model->Add(array(
					'Email'=>$this->data['postData']['email'],
					'Password'=>$this->data['postData']['password'],
					'Registration'=>date("c"),
					'Languages_id'=>1
					));
                $InGroupsModel->Add(
                    array(
                        'Users_id'=>$user,
                        'UserGroups_id'=>2
                    )
                );
				if ($user) {
					$postErrors = Login();
				} else {
					$postErrors['Email'] = "Unable to add user";
				}
			}
        }		
	}
);
$controller->Add
(
	REQUEST_POST,
	'/login',
	function($controller,$route,$parameters,$models)
	{
		$err = Login();
		$controller->AddData('errors',$err);
	}
);

$controller->Add
(
	REQUEST_GET | REQUEST_POST,
	'/register',
	function($controller,$route,$parameters,$models)
	{
		$this->config['page_title'] 				= "Register";
		$this->data['PostData']['Email'] 			= "";
		$this->data['PostData']['Password'] 		= "";
		$this->data['PostData']['ConfirmPassword'] 	= "";
        $postErrors = array();

        $this->errors->Add('Post',$postErrors);
		$contentView = $controller->CreateView();
		echo $contentView->show('register.tpl.php');
	}
);
$controller->Add
(
	REQUEST_GET,
	'/registrationcomplete',
	function($controller,$route,$parameters,$models)
	{
		$config['page_title'] = "Register";
		$contentView = $controller->CreateView();
		echo $contentView->show('registrationComplete.tpl.php');		
	}
);
$controller->Add
(
	REQUEST_GET,
	'/view',
	function($controller,$route,$parameters,$models)
	{
		$this->config['page_title'] = "Users";
		$contentView = $controller->CreateView();
		echo $contentView->show('user.tpl.php');		
	}
);
$controller->Add
(
	REQUEST_GET | REQUEST_POST,
	'/login',
	function($controller,$route,$parameters,$models)
	{
		
		$this->config->Add('page_title',"Login");
		$contentView = $controller->CreateView();
		echo $contentView->show('login.tpl.php');		
	}
);
$controller->Add
(
	REQUEST_GET | REQUEST_POST,
	'/logout',
	function($controller,$route,$parameters,$models)
	{
		unset($_SESSION['user']);
		SetLoggedOutGroups();
		header("location:/users/login");
	}
);
$controller->Add
(
	REQUEST_GET | REQUEST_POST,
	'/loggedin',
	function($controller,$route,$parameters,$models)
	{
		$this->config->Add('page_title',"Login");
		$contentView = $controller->CreateView();
		echo $contentView->show('loginComplete.tpl.php');
	}
);
$controller->Add
(
	REQUEST_GET | REQUEST_POST,
	'/$',
	function($controller,$route,$parameters,$models)
	{
		$this->config->Add('page_title',"Users");
		$contentView = new View($this->theme,$this->data,$this->config);
		echo $contentView->show('users.tpl.php');
	}
);
$controller->Run($_SERVER['REQUEST_URI'],CheckRequestMethod());