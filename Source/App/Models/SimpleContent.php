<?php
namespace Models;

class SimpleContent
{
    /**
     * PDO local connection
     * @var \PDO
     */
    protected $db;
    /**
     * Tyoe Name
     * @var String
     */    
    protected $name;
    /**
     * Content model
     * @var \Models\Content 
     */
    protected $cModel;
    /**
     * Content type model
     * @var \Models\ContentTypes 
     */
    protected $ctm;
    /**
     * Content type fields model
     * @var \Models\ContentTypeFields 
     */
    protected $ctfm;

    public function __Construct($db,$name)
    {
        $this->db               = $db;
        $this->name             = $name;
        $this->cModel		= new \Models\Content(\Common::LocalDB());
        $this->ctm 		= new \Models\ContentTypes(\Common::LocalDB());
        $this->contentDataModel = new \Models\ContentData(\Common::LocalDB());
        $this->ctfm             = new \Models\ContentTypeFields(\Common::LocalDB());        
    }
    /**
     * Get Content Id
     * @param int $id
     * @return \Models\Content
     */
    public function Get($id)
    {
        $groups = \Users::GetGroups();      
        return $this->cModel->Get($id,array('UserGroups'=>$groups));
    }
    /**
     * 
     * @param string $type
     * @return \models\ContentType
     */
    public function GetType($type = null)
    {
        $type = ($type?$type:$this->name);
        return $this->ctm->Get($type);
    }
    /**
     * Get the fields for the content type
     * @return array
     */
    public function GetContentFieldOptions()
    {
        $options = array();
        $fields = $this->GetFields();
        $groups = \Users::GetGroups();
        foreach ($fields as $field)
        {
            if ( strtolower($field->Type) == "content")
            {						
                $subData                    = $this->cModel->GetContentByTypeId($field->TypeData,$groups);
                $options[$field->TypeData]  = $subData;
            }
        }
        return $options;
    }
    /**
     * Fetches the content for properties that are content
     * @param array $data
     * @return \Models\Content
     */
    public function GetContentData($data)
    {
        foreach ($this->GetFields() as $field)
        {
            if ( strtolower($field->Type) == "content")
            {
                $property = $field->Name;
                if ( property_exists($data,$property) && $data->$property != null && $data->$property > 0 )
                {
                    $fieldData = $this->cModel->Get($data->$property);
                    $data->$property = $fieldData;
                }		
            }				
        }
        return $data;
    }
    /**
     * Get all fields for this content type
     * @return array
     */
    public function GetFields()
    {
        return $this->ctfm->GetAll(array('Name'=>$this->name),"Name=:Name");        
    }
}
