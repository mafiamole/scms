<?php

namespace Models;
/**
 * Content model
 */
class Content implements \IModel
{
    /**
     * Content ID
     * @var int
     */
    public $id;
    /**
     * Get parent ID
     * @var int
     */
    public $Parent_id;
    /**
     * URL of the content string
     * @var string
     */
    public $URL;
    /**
     * Content type id
     * @var int
     */
    public $ContentTypes_id;
    /**
     * ID of the author
     * @var int
     */
    public $Author_Id;
    /**
     * Applications App ID
     * @var int 
     */
    public $Applications_Appid;
    /**
     * Content Data property. Not a column
     * @var Array
     */
    public $Data;
    /**
     * Local Database 
     * @var PDO
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
        $this->dbHelper = new \MySQLModelHelper($db,"Content",GetPublicFields($this,array('Data')));
    }
    /**
     * 
     * @param int $id ID of the contentID to use
     * @param array $options Array of options to use
     * @return \Models\Content
     */
    public function Get($id, $options = array())
    {        
        $ugs                = Content::PrepareGroupData($options);
        $data               = $ugs;
        $userGroups         = array_keys($data);
        $placeHolderStr     = implode(",",$userGroups);
        $data[':contentId'] = $id;
        $query              = "SELECT * FROM `view_content` WHERE `Id` = :contentId AND `UserGroups_Id` IN ($placeHolderStr) group by Content_Id;";
        $prep = $this->db->prepare($query);
        $prep->execute($data);
        $content = $prep->fetch(\PDO::FETCH_OBJ);
   
        if (!$content || count($content) == 0)
        {      
            $this->ShowError($prep);
        }
        $content = $this->PrepareContent($contentData);
        $cd = new ContentData($this->db);
        $contentData = $cd->GetContentDataByContentId($id, $ugs);
        $content = $this->PrepareContentData($content,$contentData);
        return $content;       
    }
    /**
     * Get all content Items
     * @param Array $options Options
     * @return Array
     */
    public function GetAll($options = array())
    {
        $records = $this->dbHelper->GetAll();
        $contentRecords = array();
        $cd = new ContentData($this->db);
        foreach ($records as $record)
        {
            $content = $this->PrepareContent($contentData);
            $contentData = $cd->GetContentDataByContentId($id, $ugs);
            $content = $this->PrepareContentData($record,$contentData);
            $contentRecords[] = $content;
        }
        return $contentRecords;
    }
    /**
     * Get content items for a specific type
     * @param string $typeName
     * @param array $groups
     * @return Array of \Models\Content
     */
    public function GetContentByType($typeName,$groups)
    {
        $userGroups             = Content::PrepareGroupData(array('UserGroups'=>$groups));
        $userGroupPlaceHolders  = array_keys($userGroups);
        $placeHolderStr         = implode(",",$userGroupPlaceHolders);
        $data                   = $userGroups;
        $data[':typeName']      = $typeName;
        $query                  = "SELECT * FROM `view_content` WHERE `TypeName` = :typeName AND `UserGroupId` IN ($placeHolderStr) group by ContentId;";
        $prep                   = $this->db->prepare($query);
        $prep->execute($data);
        $content = $prep->fetchAll(\PDO::FETCH_OBJ);
        if (!$content || count($content) == 0)
        {      
            $this->ShowError($prep);
        }
        $returnData = $this->PrepareContentItems($content,$groups);
        return $returnData;      
    }
    /**
     * 
     * @param int $parentId
     * @param array $groups
     * @return array
     */
    public function GetChildItems($parentId,$groups)
    {
        $userGroups             = Content::PrepareGroupData(array('UserGroups'=>$groups));
        $userGroupPlaceHolders  = array_keys($userGroups);
        $placeHolderStr         = implode(",",$userGroupPlaceHolders);
        $data                   = $userGroups;
        $data[':Parent_id']     = $parentId;
        $query                  = "SELECT * FROM `view_content` WHERE `Parent_Id` = :Parent_id AND `UserGroupId` IN ($placeHolderStr)";
        $prep                   = $this->db->prepare($query);
        $prep->execute($data);
        $content                = $prep->fetchAll(\PDO::FETCH_OBJ);
        if (!$content || count($content) == 0) { $this->ShowError($prep); }
        $returnData = $this->PrepareContentItems($content,$groups);        
        return $returnData;
    }
    /**
     * Add content
     * @param array $data
     * @param array $options
     * @return int
     */
    public function Add($data, $options = array())
    {
        $id = $this->dbHelper->Add($data,array('URL','ContentTypes_id','Users_Id','Applications_AppId'));
        $contentData = new \Models\ContentData($this->db);
        if (array_key_exists('Data', $data) && is_array($data['Data']))
        {
            foreach ($data['Data'] as $lang => $cd_data)
            {
                $contentData->Add($cd_data);
            }

        }
        else
        {
            $contentData->PopulateContentData($data);
        }
        return $id;
    }
    /**
     * Edit content
     * @param array $data
     * @param array $options
     * @return bool
     */
    public function Edit($data, $options = array())
    {
        return $this->dbHelper->Edit($data,"`Id` = :Id");
    }
    /**
     * Delete a content item
     * @param int $id
     * @param array $chain
     * @param array $options
     * @return bool
     */
    public function Delete($id, $chain = false, $options = array())
    {
        return $this->dbHelper->Delete($id);
    }
    /**
     * Prepares user group list for PDO query
     * @param array $options
     * @return array
     */
    public static function PrepareGroupData($options = array())
    {
        $data = array();
        if (array_key_exists('UserGroups', $options))
        {
            foreach ($options['UserGroups'] as $userGroup)
            {
                if (  is_numeric($userGroup) )
                {
                    $key = ":usergroup{$userGroup}";
                    $data[$key] = $userGroup;
                }
                else
                {
                    $key = ":usergroup{$userGroup->Id}";
                    $data[$key] = $userGroup->Id;
                }
            }            
        }
        return $data;
    }
    /**
     * Prepare content record
     * @param array $viewData
     * @return \Models\Content
     */
    protected function PrepareContent($viewData)
    {
        $returnData = new Content($this->db);
        $returnData->id                     = GetFieldOrDefault($viewData,'Id',0);
        $returnData->Parent_Id              = GetFieldOrDefault($viewData,'Parent_Id',null);        
        $returnData->URL                    = GetFieldOrDefault($viewData,'URL','/');
        $returnData->ContentTypes_id        = GetFieldOrDefault($viewData,'Type_Id',0);
        $returnData->ContentTypes_name      = GetFieldOrDefault($viewData,'Type_Name',"No Type");
        $returnData->Users_id               = GetFieldOrDefault($viewData,'Author_Id',1);
        $returnData->Users_email            = GetFieldOrDefault($viewData,'Author_Email',"no-email@example.com");
        $returnData->Data                   = array();
        return $returnData;
    }
    /**
     * Fetches additional content data. If $useLangage is populated, then this is merged into the content item, otherwise it is added to an array property called Data
     * @param \Models\Content $content
     * @param array $data
     * @param bool|int $useLangage
     * @return \Models\Content
     */
    protected function PrepareContentData(Content $content,array $data,$useLangage = false)
    {
        if ($useLangage)
        {
            $content->Data = new \stdClass();
            foreach ($data as $d)
            {
                if ($d->Languages_Id == $useLangage && !property_exists($content, $d->type))
                {
                    $content->$type = $d->Value;
                }
            }
        }
        else
        {
            foreach ($data as $d)
            {
                $content->Data[$d->Languages_Id][$type] = $d->Value;
            }
        }       

        return $content;
    }
    /**
     * Prepare array of content records
     * @param array $viewData
     * @param array $groups
     * @return array
     */
    protected function PrepareContentItems($viewData,$groups)
    {
        $cd = new ContentData($this->db);
        $returnData = array();
        foreach ($viewData as $ct)
        {
            $id = $ct->Content_Id;
            $content = $this->PrepareContent($ct);
            $contentData = $cd->GetContentDataByContentId($id, $groups);
            $content = $this->PrepareContentData($content,$contentData);
            $returnData[$id] = $content;      
        }
        return $returnData;
    }
    /**
     * Display and throw exception for database error
     * @param type $prep
     * @throws \Exception
     */
    protected function ShowError($prep)
    {
        $errInfo = $prep->errorInfo();
        if ( $errInfo[0] != "00000" )
        {
               Debug($prep->errorInfo());
        }
        throw new \Exception("Access Denied");        
    }   
}

