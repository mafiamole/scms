<?php
namespace Models;

class ContentTypeFields implements \IModel
{
    /**
     * Content Type field ID
     * @var int
     */
    public $Id;
    /**
     * Type id of the field
     * @var type 
     */
    public $ContentTypes_Id;
    /*
     * Name of the content type
     */
    public $Name;
    /**
     * Type of the content type field
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

    public function __construct($db)
    {
        $this->db 	= $db;
        $this->dbHelper = new \MySQLModelHelper($db,"ContentTypeFields",GetPublicFields($this));
    }
    /**
     * Add a Content Type field
     * @param array $data
     * @param array $options
     */
    public function Add($data,$options = array())
    {
        $this->dbHelper->Add($data,array('ContentType_Id','Name','Type'));
    }
    /**
     * Edit a Content Type field
     * @param array $data
     * @param array $options
     */    
    public function Edit($data,$options = array())
    {
        $this->dbHelper->Edit($data);
    }
    /**
     * Remove a content type field record
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
     * 
     * @param type $id
     * @param type $options
     * @return \models\ContentTypeFields
     */
    public function Get($id, $options = array())
    {
        $record = $this->dbHelper->Get(array('id'=>$id),"`Id` = :id");
        return PrepareRecord($this->db, __CLASS__, $record);
    }
    /**
     * Get all content type fields
     * @param array $options
     * @return array
     */
    public function GetAll($options = array())
    {
        $data   = array();
        $where  = "";
        if ( array_key_exists('Where',$options) )
        {
            $where = $options['Where'];
        }
        if ( array_key_exists('Data',$options) )
        {
            $data = $options['Data'];
        }        
        $records = $this->dbHelper->GetAll($data,$where);
        $ctfs = array();
        foreach ($records as $record)
        {
            $ctfs[] = PrepareRecord($this->db, __CLASS__, $record);
        }
        return $ctfs;
    }
}