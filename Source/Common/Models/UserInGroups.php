<?php
namespace Models;
/**
 * UserInGroups Model
 */
class UserInGroups implements \IModel
{
    public $Users_id;
    public $USerGroups_id;
    protected $db;
    protected $dbHelper;

    public function __construct($db)
    {
        $this->db 	= $db;
        $this->dbHelper = new \MySQLModelHelper($db,"usersingroups",GetPublicFields($this));
    }
    /**
     * Add a user in a group record
     * @param Array $data
     * @param Array $options
     */
    public function Add($data, $options = array())
    {
        $this->dbHelper->Add($data,array('Users_id','UserGroups_id'));
    }
    /**
     * Edit a record
     * @param Array $data
     * @param Array $options
     */
    public function Edit($data, $options = array())
    {
        $this->dbHelper->Edit($data);
    }
    /**
     * Remove a user in a group record
     * @param int $id
     * @param bool $chain
     * @param array $options
     * @return type
     */
    public function Delete($id,$chain = false, $options = array())
    {
        return $this->dbHelper->Delete($id);
    }
    /**
     * Get a record by id
     * @param int $id
     * @param array $options
     * @return array
     */
    public function Get($id, $options = array())
    {
        return $this->dbHelper->Get(array('Id'=>$id), "`Id` = :Id");   				
    }
     /**
     * Get a record by id
     * @param int $id
     * @param array $options
     * @return array
     */   
    public function GetAll($options = array())
    {
        return $this->dbHelper->GetAll();   		
    }
}
