<?php
namespace Models;
function FindContent($id,$data)
{
    foreach($data as $d)
    {
        if ($d->ContentId == $id)
        {
            return $d;
        }
    }
    return null;
}
class SimpleContent
{
 	protected $db;
	protected $name;
	protected $cModel;
	protected $cLangModel;
	protected $ctm;
	protected $ctfm;

	public function __Construct($db,$name)
	{
		$this->db				= $db;
        $this->name             = $name;
		$this->cModel		    = LoadModel(\Common::LocalDB(),"Content","ContentModel");
		$this->cLangModel      	= LoadModel(\Common::LocalDB(),"Content","ContentLangModel");
		$this->ctm 				= LoadModel(\Common::LocalDB(),"Content","ContentTypesModel");
		$this->contentDataModel = LoadModel(\Common::LocalDB(),"Content","ContentDataModel");
		$this->ctfm             = LoadModel(\Common::LocalDB(),"Content","ContentTypeFieldsModel");
	}
    public function Get($id)
    {
        $data =  $this->cModel->Get($id);
		
		return $data;
    }

	public function GetType($type = null)
    {
        $type = ($type?$type:$this->name);
        return $this->ctm->Get($type);
    }

	public function GetContentFieldOptions()
	{
		$options = array();
		$fields = $this->GetFields();
		$groups = GetGroups();
		foreach ($fields as $field)
		{
			if ( strtolower($field->Type) == "content")
			{						
				$subData 					= $this->cModel->GetContentByTypeId($field->TypeData,$groups);
				$options[$field->TypeData] 	= $subData;
			}
		}
		return $options;
	}
	/**
	 *	Fetches the content for properties that are content
	 *
	 *
	 **/
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
    public function GetFields()
    {
        return $this->ctfm->GetContentTypeFieldsByName($this->name);        
    }
}

class Stories extends SimpleContent
{
	public function __Construct($db,$name="Story")
	{
        parent::__Construct($db,$name);
	}

    public function Get($id)
    {
        $story = $this->cModel->Get($id);
        
    }

    public function GetAll()
    {
        $groups     = GetGroups();
        $cats = $this->cModel->GetContentByType("StoryCategory",$groups);
		foreach ($cats as $catKey => $cat)
		{
			$catStories = $this->cModel->GetChildItems($cat->ContentId,$groups);
			$cats[$catKey]->Stories = $catStories;
		}
		return $cats;
    }

    public function GetPosts($questId)
    {
        $groups = GetGroups();
        $content->Posts =  $contentModel->GetChildItems($questId,$groups);
        $ranks = new Ranks();
        $ranks = $ranks->GetAll();
        $characters = new Characters();
        $characters = $characters->GetAll();
        foreach ($content->Posts as $key => $post)
        {

            if (isset($post->Characters))
            {
                $characterIds = explode(",",$post->Characters);
                $content->Posts[$key]->Characters = array();
                foreach($characterIds as $char)
                {
                    $foundChar = FindContent($char,$characters);
                    if ( $foundChar )
                    {
                        $foundChar->Rank = FindContent($foundChar->Rank,$ranks);
                        $content->Posts[$key]->Characters[] = $foundChar;
                    }
                }
            }
            else
            {
                $post->Characters = array();
            }

        }        
    }
    public function GetCategories()
    {
        $groups = GetGroups();
        $questCats 			= $this->cModel->GetContentByType("StoryCategory",$groups);
        foreach ($questCats as $key => $questCat)
        {
            $questCats[$key]->Quests = $contentModel->GetChildItems($questCat->ContentId,$groups);
            foreach ($questCats[$key]->Quests as $QKey => $quest)
            {
                $questCats[$key]->Quests[$QKey]->Posts = $contentModel->GetChildItems($quest->ContentId,$groups); // TODO: Change to a count!
            }
	   }
        return $questCats;
    }
        
    public function Post($post,$questId)
    {
        $quest = $this->Get($questId);
        $pType = $this->GetType("StoryPost");
        if ($quest)
        {
            $post['Parent_id'] 			= $questId * 1;
            $post['URL']				= "/quests/view/";

            $post['Users_id'] 			= $_SESSION['user']->Id;
            $post['ContentTypes_id'] 	= $contentType->Id;
            $post['Applications_AppId'] 	= 1;
            $post['Languages_id']		= $_SESSION['user']->Languages_id;
            $post['Keywords'] 			= "";

            $characters 				= array();
            foreach($post['UsersCharacters'] as $char)
            {
                $characters[] = $char * 1;
            }
            /*
            foreach($post['OthersCharacters'] as $char)
            {
                $characters[] = $char * 1;
            }*/		
            $post['Characters']			= implode(",",$characters);
            $contentId                  = $this->cModel->Add($post);
            $post['Content_id']         = $contentId;
            $contentLangId              = $cLangModel->Add($post);
            return true;       
        }
        return false;
    }
}

class Simm extends SimpleContent
{
	public function __Construct($db,$name="PositionGroup")
	{
        parent::__Construct($db,$name);
	}
    public function Manifest()
    {
        $groups = GetGroups();
        $positionGroupsData = $this->cModel->GetContentByType($this->name,$groups);
        foreach ($positionGroupsData as $key => $pg) {
            $positionGroupsData[$key]->Positions = array();
            $positions = $this->cModel->GetChildItems($pg->ContentId,$groups);

            foreach ( $positions as $key2 => $pos)
            {
                if ( isset($pos->Character) && $pos->Character && ($pos->Character*1) > 0 )
                {
                    $character = $this->cModel->Get($pos->Character*1);
                    if ($character) {
                        //$positionGroupsData[$key]->Positions[$key] = new stdclass();
                        $canEdit = (\System::LoggedIn() && $_SESSION['user']->Id == $character->UserId);
                        $character->Rank = $this->cModel->Get($character->Rank);
                        $character->CanEdit = $canEdit;
                        $positions[$key2]->Character = $character;
                    } else {
                        $positions[$key2]->Character = new stdclass();
                    }
                }

            }
            $positionGroupsData[$key]->Positions = $positions;
        }
        return $positionGroupsData;
    }
    public function Description()
    {
        return array(
            'Title' => "Simm Description",
            "Body" => "Bacon ipsum dolor amet alcatra porchetta hamburger capicola shoulder. Jerky turducken bresaola corned beef pancetta pig, turkey pastrami. Jowl ham hock tenderloin shoulder leberkas tongue turducken tri-tip, corned beef cow spare ribs. Shankle pork capicola, doner fatback alcatra pig beef ham hock cow chicken landjaeger. Fatback bresaola drumstick chicken."
        );        
    }
}

class Ranks extends SimpleContent
{
	public function __Construct($db,$name="Ranks")
	{
        parent::__Construct($db,$name);
	}    
}

class Characters extends SimpleContent
{
	public function __Construct($db,$name="Character")
	{
        parent::__Construct($db,$name);
	}
	public function Get($id)
	{        
        $character = parent::Get($id);
        $character = $this->GetContentData($character);
        return $character;		
	}
	public function Add($data)
	{
		
        if (!(isset($_SESSION['user']) && isset($_SESSION['user']->Id)))
        {
            return false;
        }
        $groups                         = GetGroups();
        // We need to filter the darta.
        $characterType                  = $this->GetType();
        
        $data['ContentTypes_id']        = $characterType->Id;
        $data['Users_id']               = $_SESSION['user']->Id * 1;
        $data['Applications_AppId']     = 1;
        $contentId                      = $this->cModel->Add($data);
        $data['Content_id']             = $contentId;
        $data['Languages_id']           = $_SESSION['user']->Languages_id * 1;
        $data['Keywords']               = "";
        $data['Description']            = "";
        foreach($data as $key => $value)
        {
            $data[str_replace("_"," ",$key)] = $value;
        }
        $contentLangId = $this->cLangModel->Add($data);


        $positionData                   = array();
        $positionData['Status']         = "Closed";
        $positionData['Character']      = $contentId;
        $success = $this->contentDataModel->EditAllContentData($positionData,$data['Position']*1,$groups);
        return $contentId;
	}
	public function Edit($data)
	{
        if (!$_SESSION['user']->Id)
        {
            return false;
        }
        $groups                        = GetGroups();

        $characterType 				   = $this->GetType();
        $PosTypeModel 				   = $this->ctm->Get("Position");
        $data['ContentId'] 		       = $data['ContentId']*1;
        $data['ContentTypes_id'] 	   = $characterType->Id;
        $data['Users_id'] 			   = $_SESSION['user']->Id;
        $data['Applications_AppId']    = 1;
        $data['Id']                    = $data['ContentId'];
        $contentId                     = $this->cModel->Edit($data);
        $data['Id']                    = $data['ContentLangId'];
        $data['Content_id']            = $data['ContentId']*1;
        $data['Languages_id']          = $_SESSION['user']->Languages_id *1;
        $data['Keywords']              = "";
        $data['Description']           = "";
        foreach($data as $key => $value)
        {
            $data[str_replace("_"," ",$key)] = $value;
        }
        try {
            $contentLangId                  = $this->cLangModel->Edit($data,$groups);
            $positionData                   = array();
            $positionData['Status']         = "Closed";
            $positionData['Character']      = $contentId;
            $success                        = $this->contentDataModel->EditAllContentData($positionData,$data['Position']*1,$groups);
        } catch(Exception $exception) {

        }
        return $contentId;
	}
                                                
}