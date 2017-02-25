<?php

namespace models;

class ContentAccessRightsModel implements Model
{
    /**
     * Content access right Id
     * @var int
     */
    public $Id;
    /**
     * Content type ID
     * @var int
     */
    public $ContentTypes_id;
    /**
     * User group id that the access record is against
     * @var int
     */
    public $UserGroups_id;
    /**
     * Access value
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
        $this->db = $db;
        $this->dbHelper = new MySQLModelHelper($db, "ContentAccessRights", GetPublicFields($this));
    }

    public function Add($data)
    {
        $this->dbHelper->Add($data, array('ContentTypes_id', 'UserGroups_id', 'Value'));
    }

    public function Edit($data)
    {
        $this->dbHelper->Edit($data);
    }

    public function Delete($id, $chain = false)            
    {
        return $this->dbHelper->Delete($id);
    }

    public function Get($id)
    {
        return $this->dbHelper->Get(array('id' => $id), "`Id` = :id");
    }

    public function GetAll()
    {
        return $this->dbHelper->GetAll();
    }

    public function GetContentAccess($contentDataId)
    {
        return $this->dbHelper->Get(array('id' => $contentDataId), "`ContentTypes_id` = :id");
    }

    public function GetGroupAccess($group)
    {
        return $this->dbHelper->Get(array('id' => $group), "`UserGroups_id` = :id");
    }

    public function GetGroupContentAccess($group, $content)
    {
        return $this->dbHelper->Get(array('id' => $contentDataTypeId), "`UserGroups_id` = :id");
    }

    public function GetLoggedOutContentDataAccess($content)
    {
        $query = "SELECT * FROM `loggedoutcontentaccessrights` WHERE `ContentTypes_id` = :content";
        $prepQry = $this->db->prepare($query);
        $prepQry->execute();
        return $prepQry->fetch();
    }

}
