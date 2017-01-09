<?php
namespace models;

class Languages implements \IModel
{
	protected $db;
	protected $dbHelper;
	
	public function __construct($db)
	{
		$this->db 		= $db;
		$this->dbHelper = new \MySQLModelHelper($db,"languages",array(
			'Id','Name','Direction'
		));
	}
	
	public function Add($data)
	{
		return $this->dbHelper->Add($data,array('Name','Direction'));
	}
	
	public function Edit($data)
	{
		return $this->dbHelper->Edit($data,"`Id` = :id");
	}
	public function Delete($id,$chain = false)
	{
		return $this->dbHelper->Delete($id);
	}
	public function Get($id)
	{
		return $this->dbHelper->Get(array('id'=>$id),"`Id` = :id");
	}
	public function GetAll()
	{
		return $this->dbHelper->GetAll();
	}
	public function Search($parameters)
	{
	}
}
class UserDataModel implements \IModel
{
	protected $db;
	protected $dbHelper;
	
	public function __construct($db)
	{
		$this->db 		= $db;
		$this->dbHelper = new \MySQLModelHelper($db,"userdata",array(
			'Id','UserDataTypes_id','Users_id','Value'
		));
	}
	public function Add($data)
	{
		return $this->dbHelper->Add($data,array('UserDataTypes_id','Users_id','Value'));	
	}
	public function Edit($data)
	{
		if ( !isset($data['Id']) ) {
			throw new Exception("No id for UserData::Edit");
		}
		return $this->dbHelper->Edit($data,"Where `Id` = :id");
	}
	public function Delete($id,$chain = false)
	{
		return $this->dbHelper->Delete($id);
	}
	public function Get($id)
	{
		$query		= "SELECT `Id`,`Name`,`Type` FROM `view_userdata` WHERE `Id` = :id";
		$prepQry 	= $this->db->prepare($query2);
		$prepQry->execute(array('Id'=>$id));
		return $prepQry->fetch();		
	}
	public function GetAll()
	{
		$query		= "SELECT `Id`,`Name`,`Type` FROM `view_userdata`";
		$prepQry 	= $this->db->prepare($query);
		$prepQry->execute();
		return $prepQry->fetch();		
	}
	public function GetUsersData($userId)
	{
		$query		= "SELECT `Id`,`Name`,`Type` FROM `view_userdata` WHERE `UserId` = :userId";
		$prepQry 	= $this->db->prepare($query);
		$prepQry->execute(array('userId'=>$userId));
		return $prepQry->fetch();			
	}
	public function Search($parameters)
	{
	
	}
	protected function FindExisting($UserDataTypes_id,$Users_id)
	{
		$query		= "SELECT `Id`,`Name`,`Type` FROM `view_userdata`";
		$prepQry 	= $this->db->prepare($query);
		$prepQry->execute();
		return $prepQry->fetch();		
	}
}
class UserDataTypes implements \IModel
{
	protected $db;
	protected $dbHelper;
	
	public function __construct($db)
	{
		$this->db 		= $db;
		$this->dbHelper = new \MySQLModelHelper($db,"userdatatypes",array(
			'Id','Name','Type'
		));
	}
	public function Add($data)
	{
		$this->dbHelper->Add($data,array('Name','Type'));
	}
	public function Edit($data)
	{
		$this->dbHelper->Edit($data);
	}
	public function Delete($id,$chain = false)
	{
		return $this->dbHelper->Delete($id);
	}
	public function Get($id)
	{
		
	}
	public function GetAll()
	{
		$query		= "SELECT `Id`,`Name`,`Type` FROM `userdatatypes`";
		$prepQry 	= $this->db->prepare($query);
		$prepQry->execute();
		return $prepQry->fetch();		
	}
	public function Search($parameters)
	{
		
	}
}

class UserGroupsModel implements \IModel
{
	protected $db;
	protected $dbHelper;
	
	public function __construct($db)
	{
		$this->db 		= $db;
		$this->dbHelper = new \MySQLModelHelper($db,"usersgroups",array(
			'Id','Name','Created','AssignLoggedOutUsers'
		));
	}
	public function Add($data)
	{
		if (!$data['Created']) {
			$data['Created'] = date("c"); // ISO 8601 date
		}
		$this->dbHelper->Add($data,array('Name','Created'));
	}
	public function Edit($data)
	{
		$this->dbHelper->Edit($data);
	}
	public function Delete($id,$chain = false)
	{
		return $this->dbHelper->Delete($id);
	}
	public function Get($id)
	{
		$query		= "SELECT `Id`,`Name`,`Type` FROM `usergroups` Where `Id` = ?";
		$prepQry 	= $this->db->prepare($query);
		$prepQry->execute(array($id));
		return $prepQry->fetch(\PDO::FETCH_OBJ);				
	}
	public function GetAll()
	{
		$query		= "SELECT `Id`,`Name`,`Type` FROM `usergroups`";
		$prepQry 	= $this->db->prepare($query);
		$prepQry->execute();
		return $prepQry->fetchAll(\PDO::FETCH_OBJ);		
	}
	public function UsersInGroups($groupId)
	{
		$query		= "SELECT `UserId`,`email`,`password`,`AuthenticationToken` FROM `view_usersingroups` WHERE `Id` = :groupId";
		$prepQry 	= $this->db->prepare($query);
		$prepQry->execute(array(':groupId'=>$groupId));
		return $prepQry->fetchAll(\PDO::FETCH_OBJ);		
	}
	public function UserInGroups($userId,$groupId)
	{
		$query		= "SELECT `UserId`,`email`,`password`,`AuthenticationToken` FROM `view_usersingroups` WHERE `UserId` = :userId AND `Id` = :groupId";
		$prepQry 	= $this->db->prepare($query);
		$prepQry->execute(array(':userId'=>$userId,':groupId'=>$groupId));
		return $prepQry->fetchAll(\PDO::FETCH_OBJ);			
	}
	public function UsersGroups($userId)
	{
		$query		= "SELECT `Id`,`Name`,`Created`,`AssignLoggedOutUsers` FROM `view_usersgroups` WHERE `UserId` = :userId";
		$prepQry 	= $this->db->prepare($query);
		$done = $prepQry->execute(array(':userId'=>$userId));
		if (!$done) {
			Debug($query);
			Debug($prepQry->errorInfo());
		}
		$data = $prepQry->fetchAll(\PDO::FETCH_OBJ);
		return $data;
	}
	public function Search($parameters)
	{
		
	}	
}

class UserInGroupsModel implements \IModel
{
	protected $db;
	protected $dbHelper;
	
	public function __construct($db)
	{
		$this->db 		= $db;
		$this->dbHelper = new \MySQLModelHelper($db,"usersingroups",array(
			'Users_id','UserGroups_id'
		));
	}
	public function Add($data)
	{
		$this->dbHelper->Add($data,array('Users_id','UserGroups_id'));
	}
	public function Edit($data)
	{
		$this->dbHelper->Edit($data);
	}
	public function Delete($id,$chain = false)
	{
		return $this->dbHelper->Delete($id);
	}
	public function Get($id)
	{
		$query		= "SELECT Users_id','UserGroups_id' FROM `usersingroups` Where `Users_id` = ?";
		$prepQry 	= $this->db->prepare($query);
		$prepQry->execute(array($id));
		return $prepQry->fetch();				
	}
	public function GetAll()
	{
		$query		= "SELECT Users_id','UserGroups_id' FROM `usersingroups`";
		$prepQry 	= $this->db->prepare($query);
		$prepQry->execute();
		return $prepQry->fetchAll(\PDO::FETCH_OBJ);		
	}
	public function Search($parameters)
	{
		
	}
}

class UsersModel implements \IModel
{
	protected $db;
	protected $app;
	protected $dbHelper;
	
	public function __construct($db,$app)
	{
		$this->db = $db;
		$this->app = $app;
		$this->dbHelper = new \MySQLModelHelper($db,"users",array(
			'Id','Email','Password','Registration','LastLogin','SSRFEmail','Languages_id','AuthgenticationToken','Admin'
		));	
	}
	public function Add($data)
	{
		if ( array_key_exists('email',$data) ) {
			$existingUser = $this->FindExistingByEmail($data['email']);
		} else {
			$existingUser = false;
		}
		if ( $existingUser ) throw new Exception("Email already in use");
		// Get extra data types
		$userDataTypes = new UserDataTypes($this->db,$this->app);
		$dataTypes = $userDataTypes->GetAll();
		
		$userDataItems = array();
		if ( is_array($dataTypes) ) {
			foreach ($dataTypes as $dataType)
			{
				$key = $dataType['Name'];
				if ( !isset($data[$key]) )
				{
					// TODO: Add additional checks, validation and sanitization.
					throw new Exception("$key does not exist");
				}
				$userDataItems[] = array
				(
					'UserDataTypes_id' 	=> $dataType['Id'],
					'Users_id' 			=> 0,
					'Value'				=> $data[$key],
				);
			}		
		}
		$requiredFields = array('Email','Password','Registration','Languages_id');
		$userId = $this->dbHelper->Add($data,$requiredFields);
		if ( is_array($dataTypes) ) {
			foreach($userDataItems as $userDataItem)
			{
				$userDataItem['Users_id'] = $userId;
				$userDataTypes->Add($userDataItem);
			}
		}
		return $userId;
	}
	public function Edit($data)
	{
		if ( array_key_exists('Id',$data) ) {
			$this->dbHelper->Edit($data,"Id=:Id");
		}
	}
	public function Delete($id,$chain = false)
	{
		return $this->dbHelper->Delete($id);
	}
	public function Get($id)
	{
		$userDataTypes = new UserDataTypes($this->db,$this->app);
		
		$query			= "SELECT * FROM viewusers WHERE `Id` = ?";
		$prepQry 		= $this->db->prepare($query);
		$prepQry->execute(array($id));
		$user 			= $prepQry->fetch(\PDO::FETCH_OBJ);
		
		$extraData = $userDataTypes->UserDataModel($id);
		if ($extraData) {
			foreach ($extraData as $key => $data)
			{
				$user[$data['Name']] = $data;
			}
		}
		$userGroupsModel = new UserGroupsModel($this->db);
		$usersGroups 	= $userGroupsModel->UsersGroups($id);
		$user['groups'] = $usersGroups;
		
		return $user;
	}
	public function GetAll()
	{
		$userDataTypes = new UserDataTypes($this->db,$this->app);
		
		$query			= "SELECT * FROM View_Users";
		$prepQry 		= $this->db->prepare($query);
		$prepQry->execute(array($id));
		$users 			= $prepQry->fetchAll(\PDO::FETCH_OBJ);
		
		foreach ($users as $key => $key)
		{
			$extraData = $userDataTypes->UserDataModel($user['Id']);
			
			foreach ($extraData as $key => $data)
			{
				$users[$key][$data['Name']] = $data;
			}
			$userGroupsModel = new UserGroupsModel($this->db);
			$usersGroups 	= $userGroupsModel->UsersGroups($id);
			$users['groups'] = $usersGroups;			
		}
	
		return $users;		
	}
	public function Search($parameters)
	{
		
	}
	public function GetLoggedOutGroups() {
		$query 		= "SELECT * FROM loggedoutgroups";
		$prepQry 	= $this->db->prepare($query);
		$prepQry->execute();
		return $prepQry->fetchAll(\PDO::FETCH_OBJ);		
	}
	public function FindExistingByEmail($email)
	{
		$query			= "SELECT * FROM users WHERE `Email` = ?";
		$prepQry 		= $this->db->prepare($query);
		$prepQry->execute(array($email));
		
		$user = $prepQry->fetch(\PDO::FETCH_OBJ);
        if (!$user)
            return false;
		$userDataTypes = new UserDataModel($this->db,$this->app);
		$extraData = $userDataTypes->GetUsersData($user->Id);
		if ($extraData) {
			foreach ($extraData as $key => $data)
			{
				$key =$data['Name'];
				$user->$key = $data;
			}
		}
		$userGroupsModel = new UserGroupsModel($this->db);
		$usersGroups 	= $userGroupsModel->UsersGroups($user->Id);
		$user->groups 	= $usersGroups;
		return $user;
	}
}