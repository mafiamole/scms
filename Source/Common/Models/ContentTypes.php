<?php
namespace Models;

class ContentTypes implements \IModel
{
    public $Id;
    public $Name;
    protected $db;
    protected $dbHelper;

    public function __construct($db)
    {
        $this->db 	= $db;
        $this->dbHelper = new \MySQLModelHelper($db,"ContentTypes",GetPublicFields($this));
    }
    /**
     * Add a Content type
     * @param array $data
     * @param array $options
     */
    public function Add($data,$options = array())
    {
        $this->dbHelper->Add($data,array('Name'));
    }
    /**
     * Edit a content type
     * @param array $data
     * @param array $options
     */
    public function Edit($data,$options = array())
    {
        $this->dbHelper->Edit($data);
    }
    /**
     * Remove a content type
     * @param int $id
     * @param bool $chain
     * @param array $options
     * @return bool
     */
    public function Delete($id,$chain = false,$options = array())
    {
        return $this->dbHelper->Delete($id);
    }
    /**
     * Get a content type
     * @param int $id
     * @param array $options
     * @return \models\ContentType
     */
    public function Get($id,$options = array())
    {
        if (is_string($id) ) {
            $data = array('Name'=>$id);
            $where = "`Name` = :Name";
        } else {
            $data = array('id'=>$id);
            $where = "`Id` = :id";
        }
        $record = $this->dbHelper->Get($data,$where);
        return PrepareRecord($this->db,__CLASS__,$record);
    }
    /**
     * Get all content types
     * @param array $options
     * @return array
     */
    public function GetAll($options = array())
    {
        $records = $this->dbHelper->GetAll();	
        $contentTypes = array();
        foreach ($records as $record)
        {
            $contentTypes[] = PrepareRecord($this->db, __CLASS__, $record);
        }
        return $contentTypes;
    }
}