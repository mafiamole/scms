<?php
namespace models;
/**
 * Users Model
 */
class Users implements \IModel
{
    /**
     * User Id
     * @var int 
     */
    public $Id;
    /**
     * Users Email
     * @var string
     */
    public $Email;
    /**
     * Users Password
     * @var string
     */
    public $Password;
    /**
     * Registration Date/time
     * @var Date
     */
    public $Registration;
    /**
     * Login Date/time
     * @var Date
     */
    public $LastLogin;
    /**
     * SSRF internal
     * @var String
     */
    public $SSRFEmail;
    /**
     * Language Id
     * @var Int
     */
    public $Languages_id;
    /**
     * API authentication token
     * @var string
     */
    public $AuthenticationToken;
    /**
     * User is local admin
     * @var Boolean
     */    
    public $Admin;
    /**
     * Local Database 
     * @var PDO
     */
    protected $db;
    /**
     * App name
     * @var string
     */
    protected $app;
    /**
     * SQL helper
     * @var \MySQLModelHelper
     */
    protected $dbHelper;
    // TODO: $app
    public function __construct($db,$app ="abc")
    {
        $this->db       = $db;
        $this->app      = $app;
        $this->dbHelper = new \MySQLModelHelper($db,"users",GetPublicFields($this));	
    }
    /**
     * Creates a new user
     * @param Array $data
     * @param Array $options
     * @return Int
     * @throws Exception
     */
    public function Add($data, $options = array())
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
    /**
     * Edit a User
     * @param Array $data
     * @param Array $options
     */
    public function Edit($data, $options = array())
    {
        if ( array_key_exists('Id',$data) )
        {
                $this->dbHelper->Edit($data,"Id=:Id");
        }
    }
    /**
     * Delete a user
     * @param Int $id
     * @param bool $chain
     * @param array $options
     * @return null
     */
    public function Delete($id,$chain = false, $options = array())
    {
        return $this->dbHelper->Delete($id);
    }
    /**
     * Get a user by id
     * @param type $id
     * @param type $options
     * @return \Models\Users
     */
    public function Get($id, $options = array())
    {
        $userData       = $this->dbHelper->Get(array('Id'=>$id), "`Id` = :Id");
        $user = $this->PrepareUser($userData);
        return $user;
    }
    /**
     * Get all users
     * @param type $id
     * @param type $options
     * @return Array
     */    
    public function GetAll($options = array())
    {
        $userDataTypes  = new UserDataTypes($this->db,$this->app);

        $usersData      = $this->dbHelper->GetAll();  
        $users          = array();
        foreach ($usersData as $key => $userData)
        {
            $users[] = $this->PrepareUser($userData);		
        }

        return $users;
    }
     /**
     * Get Logged out user groups
     * @return Array
     */       
    public function GetLoggedOutGroups() {
        $query 		= "SELECT * FROM loggedoutgroups";
        $prepQry 	= $this->db->prepare($query);
        $prepQry->execute();
        return $prepQry->fetchAll(\PDO::FETCH_OBJ);		
    }
    /**
     * Get a user by emaile
     * @param type $id
     * @param type $options
     * @return \Models\Users
     */    
    public function FindExistingByEmail($email)
    {
        $query			= "SELECT * FROM users WHERE `Email` = ?";
        $prepQry 		= $this->db->prepare($query);
        $prepQry->execute(array($email));
        Debug($email);
        $userData = $prepQry->fetch(\PDO::FETCH_OBJ);
        
        if (!$userData) {
            Debug($prepQry->ErrorInfo());
            return false;
        }
        $user = $this->PrepareUser($userData);
        return $user;
    }
    /**
     * 
     * @param type $user
     * @return \models\User Description
     */
    public function PrepareUser($userData)
    {
        $user = PrepareRecord($this->db, __CLASS__, $userData);
        $userDataTypes  = new UserDataTypes($this->db,$this->app);
        $extraData = $userDataTypes->UserDataModel($id);
        if ($extraData) {
            foreach ($extraData as $key => $data)
            {
                $property = $data['Name'];
                $user->$property = $data;
            }
        }
        $userGroupsModel    = new UserGroupsModel($this->db);
        $usersGroups        = $userGroupsModel->UsersGroups($id);
        $user->groups       = $usersGroups;          
        return new \models\Users();
    }
}