<?php
require(APP_FOLDER . "/ThirdParty/password.php");
require(APP_FOLDER . "/Helpers/Validator.php");

if ( array_key_exists(1,$parameters) ) {
	$function = $parameters[1];
} else {
	$function = "";
}
$isPost = (isset($_POST) && !empty($_POST));

$this->data['postData'] = array();
$this->data['errors'] = array();
$db = Common::LocalDB();
function Login() {
	
	$sanitizer = new Sanitize($_POST);
	$email = $sanitizer->CheckEmail('email');
	$db = Common::LocalDB();
	$model = LoadModel($db,'Users','UsersModel');
	$user = $model->FindExistingByEmail($email);
    if (!$user) {
        return array('email'=>"User not found");
    }
	$validPassword =  password_verify($_POST['password'],$user->Password);
	if ( $validPassword ) {
		// Set up session
		$_SESSION['user'] = array();
		$_SESSION['user'] = $user;
		$setLastLogin = array(
			'Id' 		=> $user->Id,
			'LastLogin' => date('c')
		);
		$model->Edit($setLastLogin);
		header('location:/users/loggedIn');
		return true;
	} else {
		
		$errors = $sanitizer->GetErrors();
		$errors['password'] = "Incorrect password";
		return $errors;
	}
}
switch ( $function ) {
	case "register":
		$config['page_title'] 						= "Register";
		$this->data['postData']['email'] 			= "";
		$this->data['postData']['password'] 		= "";
		$this->data['postData']['confirmPassword'] 	= "";
		if ($isPost) {
			$sanitizer = new Sanitize($_POST);
			$this->data['postData']['email'] 	= $sanitizer->CheckEmail('email');
			$this->data['postData']['password'] = $sanitizer->CheckRegPassword('password','confirmPassword');
			if ( !$this->data['postData']['email'] ) {
				$this->data['errors']['email'] = "Invalid email address entered";
			}
			if ( !$this->data['postData']['password'] ) {
				$this->data['errors']['email'] = "Invalid email address entered";
			}			
			$model 			= LoadModel($db,'Users','UsersModel');
            $InGroupsModel  = LoadModel($db,'Users','UserInGroupsModel');
            //$groupsModel    = LoadModel($db,'Users',"UserGroupsModel");
            //$groupsModel->get(2);// TODO: make dynamic like

			$this->data['errors'] = $sanitizer->GetErrors();
			Debug($this->data['postData']);
			if ( count($this->data['errors']) == 0 ) {
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
					$this->data['errors'] = Login();
				} else {
					$this->data['errors']['email'] = "Unable to add user";
				}
			}
		}
		$contentView = new View($this->theme,$this->defaults,$this->data,$this->config);
		echo $contentView->show('register.tpl.php');
	break;
	case "registrationComplete":
		$config['page_title'] = "Register";
		$contentView = new View($this->theme,$this->defaults,$this->data,$this->config);
		echo $contentView->show('registrationComplete.tpl.php');	
	break;
	case "view":
		$config['page_title'] = "Users";
		$contentView = new View($this->theme,$this->defaults,$this->data,$this->config);
		echo $contentView->show('user.tpl.php');
	break;
	case "login":
		$config['page_title'] = "Login";		
		if ( !empty($_POST) ) {
			// process login
			$this->data['errors'] = Login();
		}
		$contentView = new View($this->theme,$this->defaults,$this->data,$this->config);
		echo $contentView->show('login.tpl.php');
	break;
	case "logout":
		unset($_SESSION['user']);
		SetLoggedOutGroups();
		header("location:/users/login");
	case "loggedIn":
		$config['page_title'] = "Login";
		$contentView = new View($this->theme,$this->defaults,$this->data,$this->config);
		echo $contentView->show('loginComplete.tpl.php');		
	break;
	default:
		$config['page_title'] = "Users";
		$contentView = new View($this->theme,$this->defaults,$this->data,$this->config);
		echo $contentView->show('users.tpl.php');
	break;	
}