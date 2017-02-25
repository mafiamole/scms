<?php
namespace Models;
/**
 * Users Group Model
 */
class UserGroups implements \IModel
{
    /**
     * User group ID
     * @var int 
     */
    public $Id;
    /**
     * User group name
     * @var string
     */
    public $Name;
    /**
     * Date created
     * @var Date/Tyime 
     */
    public $Created;
    /**
     * If this is group is assigned to users who ar enot logged in.
     * @var bool 
     */
    public $AssignLoggedOutUsers;
    
    protected $db;
    protected $dbHelper;

    public function __construct($db)
    {
        $this->db 	= $db;
        $this->dbHelper = new \MySQLModelHelper($db,"usersgroups",GetPublicFields($this));
    }
    /**
     * Create a user group
     * @param array $data
     * @param array $options
     */
    public function Add($data, $options = array())
    {
        if (!$data['Created']) {
                $data['Created'] = date("c"); // ISO 8601 date
        }
        $this->dbHelper->Add($data,array('Name','Created'));
    }
    /**
     * Edit a user group
     * @param array $data
     * @param array $options
     */    
    public function Edit($data, $options = array())
    {
        $this->dbHelper->Edit($data);
    }
    /**
     * 
     * @param int $id
     * @param bool $chain
     * @param array $options
     * @return array
     */
    public function Delete($id,$chain = false, $options = array())
    {
        return $this->dbHelper->Delete($id);
    }
    /**
     * 
     * @param int $id
     * @param array $options
     * @return \Models\UserGroups
     */
    public function Get($id, $options = array())
    {
        $record = $this->dbHelper->Get(array('Id'=>$id), "`Id` = :Id");
        return PrepareRecord($this->db,__CLASS__,$record);
    }
    /**
     * 
     * @param array $options
     * @return array
     */
    public function GetAll($options = array())
    {
        $records = $this->dbHelper->GetAll();
        $groups = array();
        foreach ($records as $record)
        {
            $groups[] = PrepareRecord($this->db,__CLASS__,$record);
        }
        return $groups;
    }
    /**
     * Get all users in a group
     * @param int $groupId
     * @return array
     */
    public function UsersInGroups($groupId)
    {
        $userModel      = new \models\Users($this->db);
        $query		= "SELECT `UserId`,`email`,`password`,`AuthenticationToken` FROM `view_usersingroups` WHERE `Id` = :groupId";
        $prepQry 	= $this->db->prepare($query);
        $prepQry->execute(array(':groupId'=>$groupId));
        $userRecords = $prepQry->fetchAll(\PDO::FETCH_OBJ);
        $users = array();
        foreach ($userRecords as $userRecord)
        {
            $users[] = PrepareRecord($this->db,"\models\Users",$userRecord);
        }
        return $users;
    }
    /**
     * 
     * @param int $userId
     * @param int $groupId
     * @return \models\Users
     */
    public function UserInGroups($userId,$groupId)
    {
        $userModel      = new \models\Users($this->db);
        $query		= "SELECT `UserId`,`email`,`password`,`AuthenticationToken` FROM `view_usersingroups` WHERE `UserId` = :userId AND `Id` = :groupId";
        $prepQry 	= $this->db->prepare($query);
        $prepQry->execute(array(':userId'=>$userId,':groupId'=>$groupId));
        $userRecord = $prepQry->fetchAll(\PDO::FETCH_OBJ);       
        $user = $userModel->PrepareUser($userRecord);
        return $user;
    }
    /**
     * Get all groups for user
     * @param int $userId
     * @return \models\UserGroups
     */
    public function UsersGroups($userId)
    {
        $query		= "SELECT `Id`,`Name`,`Created`,`AssignLoggedOutUsers` FROM `view_usersgroups` WHERE `UserId` = :userId";
        $prepQry 	= $this->db->prepare($query);
        $done = $prepQry->execute(array(':userId'=>$userId));
        if (!$done) {
                Debug($query);
                Debug($prepQry->errorInfo());
        }
        $record = $prepQry->fetchAll(\PDO::FETCH_OBJ);
        return PrepareRecord($this->db,"\models\UserGroups",$record);        
    }
}