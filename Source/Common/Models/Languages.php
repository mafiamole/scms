<?php
namespace Models;
class Languages implements \IModel
{
    /**
     * Language Id
     * @var int
     */
    public $Id;
    /**
     * Langage name
     * @var string
     */
    public $Name;
    /**
     * Language direction
     * @var string
     */
    public $Direction;
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
        $this->db       = $db;
        $this->dbHelper = new \MySQLModelHelper($db,"languages",GetPublicFields($this));
    }
    /**
     * Add a new language
     * @param array $data
     * @param array $options
     * @return int
     */
    public function Add($data, $options = array())
    {
        return $this->dbHelper->Add($data,array('Name','Direction'));
    }
   /**
     * Edit a language
     * @param array $data
     * @param array $options
     * @return int
     */
    public function Edit($data, $options = array())
    {
        return $this->dbHelper->Edit($data,"`Id` = :id");
    }
    /**
     * Remove a language
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
     * Get a language
     * @param int $id
     * @param array $options
     * @return \models\Languages
     */
    public function Get($id, $options = array())
    {
        $record = $this->dbHelper->Get(array('id'=>$id),"`Id` = :id");
        return PrepareRecord($this->db, __CLASS__, $record);
    }
    public function GetAll($options = array())
    {
        $data       = GetFieldOrDefault($options, 'Data',array());
        $where      = GetFieldOrDefault($options, 'Where');
        $records    =  $this->dbHelper->GetAll($data,$where);        
        $langs      = array();
        foreach ($records as $record)
        {
            $langs[] = PrepareRecord($this->db, __CLASS__, $record);
        }
        return $langs;
    }
}