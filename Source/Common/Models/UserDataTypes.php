<?php
namespace Models;
/**
 * User Data type model
 */
class UserDataTypes implements \IModel
{
    /**
     * User data type id
     * @var int 
     */
    public $Id;
    /**
     * Name of the data type
     * @var sting
     */
    public $Name;
    /**
     * Type of the data
     * @var sting 
     */
    public $Type;
    /**
     * Local database reference
     * @var \PDO
     */
    protected $db;
    /**
     * 
     * @var \MySQLModelHelper
     */
    protected $dbHelper;

    public function __construct($db)
    {
        $this->db 	= $db;
        $this->dbHelper = new \MySQLModelHelper($db,"userdatatypes",GetPublicFields($this));
    }
    /**
     * Add a new User Data type
     * @param array $data
     * @param array $options
     */
    public function Add($data, $options = array())
    {
        $this->dbHelper->Add($data,array('Name','Type'));
    }
    /**
     * Edit user data type
     * @param array $data
     * @param array $options
     */
    public function Edit($data, $options = array())
    {
        $this->dbHelper->Edit($data);
    }
    /**
     * Delete user data type 
     * @param int $id
     * @param bool $chain
     * @param array $options
     * @return bool
     */
    public function Delete($id,$chain = false, $options = array())
    {
        return $this->dbHelper->Delete($id);
    }
    /**
     * Get a User data type
     * @param type $id
     * @param type $options
     * @return \model\UserDataTypes
     */
    public function Get($id, $options = array())
    {
        $query		= "SELECT `Id`,`Name`,`Type` FROM `userdatatypes`";
        $prepQry 	= $this->db->prepare($query);
        $prepQry->execute();
        $record = $prepQry->fetch(\PDO::FETCH_CLASS,UserDataTypes);
        return PrepareRecord($this->db,__CLASS__,$record);
    }
    /**
     * Get all User data types
     * @param type $options
     * @return type
     */
    public function GetAll($options = array())
    {
        $query		= "SELECT `Id`,`Name`,`Type` FROM `userdatatypes`";
        $prepQry 	= $this->db->prepare($query);
        $prepQry->execute();
        $records =  $prepQry->fetchAll(\PDO::FETCH_CLASS,UserDataTypes);		
        $returnArray = array();
        foreach ($records as $record)
        {
            $returnArray = PrepareRecord($this->db,__CLASS__,$record);
        }
        return $returnArray;
    }
}