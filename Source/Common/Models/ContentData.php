<?php
namespace Models;

class ContentData implements \IModel
{
    /**
     * Content Data id
     * @var int
     */
    public $Id;
    /**
     * Content id the data is for
     * @var int
     */
    public $Content_Id;
    /**
     * Content type fields for the content data
     * @var int
     */
    public $ContentTypeFields_Id;
    /**
     * Language of teh data record
     * @var int
     */
    public $Languages_Id;
    /**
     * Value of the data item
     * @var string
     */
    public $Value;
    /**
     * Local Database 
     * @var PDO
     */
    /**
     * User group id
     * @var int
     */
    public $UserGroup;
    /**
     * User access information
     * @var string
     */
    public $Access;
    protected $db;
    /**
     * SQL helper
     * @var \MySQLModelHelper
     */    
    protected $dbHelper;

    public function __construct($db)
    {
        $this->db       = $db;
        $this->dbHelper = new \MySQLModelHelper($db,"ContentData",GetPublicFields($this));
    }
    /**
     * Add content data
     * @param array $data
     * @param array $options
     * @return int
     */
    public function Add($data,$options = array())
    {
        $ctID           = $data['ContentTypeFields_Id'];
        $cID            = $data['Content_Id'];	
        $ctfModel       = new ContentTypeFieldsModel($this->db);
        $field          = $ctfModel->Get($clID);		
        $fieldType      = strtoupper($field->Type);
        $fileTypes      = array("IMAGE","FILE");
        if ( in_array($fieldType,$fileTypes) )
        {
            $fileName = FileUpload("/Resources/Uploads/Content/{$ctID}/",$field->Name,$field->Type);
            $data['Value'] = $fileName;
        }		
        $success = $this->dbHelper->Add($data,array('Content_Id','ContentTypeFields_Id','Value'));

        return $success;
    }
    /**
     * edit content data
     * @param array $data
     * @param array $options
     * @return int
     */   
    public function Edit($data,$options = array())
    {
        $this->dbHelper->Edit($data);
    }
    /**
     * delete content data
     * @param array $data
     * @param array $options
     * @return int
     */   
    public function Delete($id,$chain = false,$options = array())
    {
        return $this->dbHelper->Delete($id);
    }
    /**
     * Get a content data item
     * @param int $id
     * @param array $options
     * @return \models\ContentData
     */
    public function Get($id,$options = array())
    {
        $record = $this->dbHelper->Get(array('id'=>$id),"`Id` = :id");
        return PrepareRecord($this->db, __CLASS__, $record);
    }
    /**
     * Get all Content Data items;
     * @param array $options
     * @return array
     */
    public function GetAll($options = array())
    {
        $data       = array();
        $where      = "";
        $records = $this->dbHelper->GetAll($data,$where);
        $contentData = array();
        foreach($records as $record)
        {
            $contentData[] = PrepareRecord($this->db, __CLASS__, $record);
        }
        return $contentData;
    }
    /**
     * Get content data by content
     * @param int $contentId
     * @param array $userGroups
     * @return array
     */
    public function GetContentDataByContentId($contentId,$userGroups)
    { 
        
        $g                      = Content::PrepareGroupData(array('UserGroups'=>$userGroups));
        $placeHolders           = array_keys($g);
        $data                   = $g;
        $userGroupsIn           = implode(",",$placeHolders);
        $data[':contentId']     = $contentId;
        $query                  = "SELECT `Id`,`Name`,`Value`,`ContentTypeFields_Id`,`Type`,`UserGroup`,`Access` "
                                . "FROM `view_contentdata` "
                                . "WHERE `Content_Id`=:contentId and `UserGroup` IN ($userGroupsIn) Group by Content_Id";
        $prep                   = $this->db->prepare($query);
        $success                = $prep->execute($data);
        if (!$success)
        {
            Debug($prep->errorCode());
        }
        $records = $prep->fetchAll(\PDO::FETCH_OBJ);
        $contentData = array();
        foreach ( $records as $record )
        {
            $contentData[] =PrepareRecord($this->db, __CLASS__, $record);
        }
        return $contentData;
    }
    /**
     * Populate content data for a content data
     * @param array $data
     * @param int $contentTypeId
     * @param int $contentLangId
     */
    public function PopulateContentData($data,$contentTypeId,$contentLangId)
    {
        $ctm = new ContentTypesModel($this->db);
        $contentTypeFields = $ctm->GetContentTypesFields($contentTypeId);
        foreach($contentTypeFields as $field) {
            $key = $field->Name;
            $value = (array_key_exists($key,$data)?$data[$key]:""); // {populate with empty value for now;
            $populateData = array(
                'Content_Id' => $contentLangId,
                'ContentTypeFields_Id'=> $field->id,
                'Value' => $value
            );
            $this->Add($populateData);
        }
    }
    /**
     * Edit content data for a content data
     * @param array $data
     * @param int $contentId
     * @param array $groups
     */
    public function EditAllContentData($data,$contentId,$languageId,$groups)
    {
        $items = $this->GetContentDataByContentId($contentId,$groups);
        foreach($items as $item)
        {
            if ( $item->Language_Id != $languageId)
                continue;
            
            $altKey = str_replace(" ","_",$item->Name);
            if ( array_key_exists($item->Name,$data) || array_key_exists($altKey,$data) )
            { 
                $value = $data[$item->Name];
                $newData = array
                (
                    'Id' => $item->DataId,
                    'Value' => $value
                );
                $success = $this->Edit($newData);
            }
            if ( array_key_exists($altKey,$_FILES) )
            {
                $value = $_FILES[$altKey];
                $newData = array
                (
                    'Id' => $item->DataId,
                    'Value' => $value
                );
                $success = $this->Edit($newData);
            }
        }
    }
}