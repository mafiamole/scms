<?php
namespace Models;
/**
 * User Data Model
 */
class UserData implements \IModel
{
    /**
     * User Data ID
     * @var int
     */
    public $Id;
    /**
     * User data type id
     * @var int
     */
    public $UserDataTypes_id;
    /**
     * Users Id
     * @var int
     */
    public $Users_id;
    /**
     * Data value
     * @var string
     */
    public $Value;

    /**
     * Data type
     * @var string
     */
    public $Type;
    /**
     * PDO instance
     * @var \PDO
     */    
    protected $db;
    /**
     * SQL helper
     * @var \MySQLModelHelper 
     */
    protected $dbHelper;
    /**
     * Construct the model
     * @param \PDO $db
     */
    public function __construct($db)
    {
            $this->db       = $db;
            $this->dbHelper = new \MySQLModelHelper($db,"userdata",GetPublicFields($this));
    }
    /**
     * Add a new user data record
     * @param array $data
     * @param array $options
     * @return int
     */
    public function Add($data, $options = array())
    {
            return $this->dbHelper->Add($data,array('UserDataTypes_id','Users_id','Value'));	
    }
    /**
     * Add a new user data record
     * @param array $data
     * @param array $options
     * @return int
     */   
    public function Edit($data, $options = array())
    {
        if ( !isset($data['Id']) ) {
                throw new Exception("No id for UserData::Edit");
        }
        return $this->dbHelper->Edit($data,"Where `Id` = :id");
    }
    /**
     * 
     * @param array $id
     * @param bool $chain
     * @param array $options
     * @return bool
     */
    public function Delete($id,$chain = false, $options = array())
    {
        return $this->dbHelper->Delete($id);
    }
    /**
     * Get a user record
     * @param type $id
     * @param type $options
     * @return type
     */
    public function Get($id, $options = array())
    {
        $data           = GetFieldOrDefault($options, 'Data',array());
        $where          = GetFieldOrDefault($options, 'Where');       
        $query		= "SELECT `Id`,`Name`,`Type` FROM `view_userdata` WHERE `Id` = :id " . $where;
        $prepQry 	= $this->db->prepare($query);
        $data['Id']     = $id;
        $prepQry->execute($data);        
        $record = $prepQry->fetch(\PDO::FETCH_OBJ);
        $userData = PrepareRecord($this->db, __CLASS__, $record);
        return $userData;
    }
    /**
     * Get all user data
     * @param type $options
     * @return \models\UserData
     */
    public function GetAll($options = array())
    {
        $data           = GetFieldOrDefault($options, 'Data',array());
        $where          = GetFieldOrDefault($options, 'Where');        
        $query		= "SELECT `Id`,`Name`,`Type` FROM `view_userdata` {$where}";
        $prepQry 	= $this->db->prepare($query);
        $prepQry->execute($data);
        $records = $prepQry->fetch(\PDO::FETCH_OBJ);
        $userDataList = array();
        foreach ($records as $record)
        {
            $userDataList = PrepareRecord($this->db, __CLASS__, $record);
        }
        return $userDataList;
    }
    /**
     * Get user data for a single user
     * @param int $userId
     * @param array $options
     * @return \models\UserData
     */
    public function GetUsersData($userId, $options = array())
    {
        $query		= "SELECT `Id`,`Name`,`Type` FROM `view_userdata` WHERE `UserId` = :userId";
        $prepQry 	= $this->db->prepare($query);
        $prepQry->execute(array('userId'=>$userId));
        $record = $prepQry->fetch(\PDO::FETCH_OBJ);			
        return PrepareRecord($this->db, __CLASS__, $record);
    }
}
