<?php
namespace models;

/**
 * Content Data Access Rights model
 */
class ContentDataAccessRights implements Model
{
    /**
     * Content Data Access Rights Id
     * @var int
     */
    public $Id;
    /**
     * Content Types field Id
     * @var int
     */
    public $ContentTypesFields_Id ;
    /**
     * User Groups Id
     * @var int
     */
    public $UserGroups_Id;
    /**
     * Value 
     * @var string
     */
    public $Value;
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

    public function __construct($db)
    {
        $this->db 		= $db;
        $this->dbHelper = new MySQLModelHelper($db,"ContentDataAccessRights",GetPublicFields($this));
    }
    /**
     * Add a new content data access right
     * @param array $data
     */
    public function Add($data)
    {
        $this->dbHelper->Add($data,array('ContentTypesFields_Id','UserGroups_id','Value'));
    }
    /**
     * Edit a content data access right
     * @param array $data
     */    
    public function Edit($data)
    {
            $this->dbHelper->Edit($data);
    }
    /**
     * Remove content data access
     * @param int $id
     * @param bool $chain
     * @return bool
     */
    public function Delete($id,$chain = false)
    {
        return $this->dbHelper->Delete($id);
    }
    /**
     * Get a single content access right by an id
     * @param int $id
     * @return \models\ContentDataAccessRights
     */
    public function Get($id)
    {
        $record = $this->dbHelper->Get(array('id'=>$id),"`Id` = :id");
        return PrepareRecord($this->db, __CLASS__, $record);
    }
    /**
     * Get all content access rights
     * @return array
     */
    public function GetAll()
    {
        $records = $this->dbHelper->GetAll();
        $arr = array();
        foreach ($records as $record)
        {
            $arr[] = PrepareRecord($this->db, __CLASS__, $record);
        }
        return $arr;
    }
    
    public function GetContentDataAccess($contentDataTypeId)
    {
        $record = $this->dbHelper->Get(array('id'=>$contentDataTypeId),"`ContentTypeFields_id` = :id");
        return PrepareRecord($this->db, __CLASS__, $record);
    }
    public function GetGroupAccess($group)
    {
        $record = $this->dbHelper->Get(array('id'=>$group),"`UserGroups_id` = :id");
        return PrepareRecord($this->db, __CLASS__, $record);
    }
    public function GetGroupContentDataAccess($group,$content)
    {
        $record = $this->dbHelper->Get(array('content'=>$content,'group'=>$group),"`ContentTypeFields_id` = :content AND `UserGroups_id` = :group");
        return PrepareRecord($this->db, __CLASS__, $record);
    }
    public function GetLoggedOutContentDataAccess($content)
    {
        $query = "SELECT * FROM `loggedoutcontentdataaccessrights` WHERE `ContentTypesFields_id` = :content";
        $prepQry 	= $this->db->prepare($query);
        $prepQry->execute();
        $record =  $prepQry->fetch();
        return PrepareRecord($this->db, __CLASS__, $record);
    }
}
