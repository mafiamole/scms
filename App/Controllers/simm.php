<?php

$isPost = (isset($_POST) && !empty($_POST));

//$contentModel=LoadModel($this->db,"Content","ContentModel");

class ManifestGroup {
	public $name;
	public $manifest;
	public function __construct($name,$manifest) {
		$this->name = $name;
		$this->manifest = $manifest;
	}
}
function Manifest()
{
	$contentModel=LoadModel(Common::LocalDB(),"Content","ContentModel");
	$groups = GetGroups();
	
	//$charactersData = $contentModel->GetContentByType("Character",$groups);

	$positionGroupsData = $contentModel->GetContentByType("PositionGroup",$groups);
	
	foreach ($positionGroupsData as $key => $pg) {
		$positionGroupsData[$key]->Positions = array();
		$positions = $contentModel->GetChildItems($pg->ContentId,$groups);
		
		foreach ( $positions as $key2 => $pos)
		{
			if ( isset($pos->Character) && $pos->Character && ($pos->Character*1) > 0 )
			{
				$character = $contentModel->Get($pos->Character*1);
				if ($character) {
					//$positionGroupsData[$key]->Positions[$key] = new stdclass();
					$character->Rank = $contentModel->Get($character->Rank);
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

function AddCharacter()
{
	if (!$_SESSION['user']->Id)
	{
		return false;
	}
	$groups = GetGroups();
	// We need to filter the darta.
	$contentModel		= LoadModel(Common::LocalDB(),"Content","ContentModel");
	$contentLangModel 	= LoadModel(Common::LocalDB(),"Content","ContentLangModel");
	$ctm = LoadModel(Common::LocalDB(),"Content","ContentTypesModel");
	$contentDataModel = LoadModel(Common::LocalDB(),"Content","ContentDataModel");
	$ctfm = LoadModel(Common::LocalDB(),"Content","ContentTypeFieldsModel");
	
	$characterType = $ctm->Get("Character");
	$PosTypeModel = $ctm->Get("Position");
	$_POST['ContentTypes_id'] = $characterType->Id;
	$_POST['Users_id'] = $_SESSION['user']->Id;
	$_POST['Applications_AppId'] =1;
	$contentId = $contentModel->Add($_POST);
	$_POST['Content_id'] = $contentId;
	$_POST['Languages_id'] = $_SESSION['user']->Languages_id;
	$_POST['Keywords'] = "";
	$_POST['Description'] = "";
	foreach($_POST as $key => $value)
	{
		$_POST[str_replace("_"," ",$key)] = $value;
	}
	$contentLangId = $contentLangModel->Add($_POST);
	
	
	$positionData = array();
	$positionData['Status'] = "Closed";
	$positionData['Character'] = $contentId;
	$success = $contentDataModel->EditAllContentData($positionData,$_POST['Position']*1,$groups);
	return $contentId;
}

function EditCharacter()
{
	if (!$_SESSION['user']->Id)
	{
		return false;
	}
	$groups = GetGroups();
	// We need to filter the darta.
	$contentModel		= LoadModel(Common::LocalDB(),"Content","ContentModel");
	$contentLangModel 	= LoadModel(Common::LocalDB(),"Content","ContentLangModel");
	$ctm 				= LoadModel(Common::LocalDB(),"Content","ContentTypesModel");
	$contentDataModel 	= LoadModel(Common::LocalDB(),"Content","ContentDataModel");
	$ctfm 				= LoadModel(Common::LocalDB(),"Content","ContentTypeFieldsModel");
	
	$characterType 				= $ctm->Get("Character");
	$PosTypeModel 				= $ctm->Get("Position");
	$_POST['ContentId'] 		= $_POST['ContentId']*1;
	$_POST['ContentTypes_id'] 	= $characterType->Id;
	$_POST['Users_id'] 			= $_SESSION['user']->Id;
	$_POST['Applications_AppId'] =1;
	$_POST['Id'] = $_POST['ContentId'];
	$contentId = $contentModel->Edit($_POST);
	$_POST['Id'] = $_POST['ContentLangId'];
	$_POST['Content_id'] = $_POST['ContentId']*1;
	$_POST['Languages_id'] = $_SESSION['user']->Languages_id;
	$_POST['Keywords'] = "";
	$_POST['Description'] = "";
	foreach($_POST as $key => $value)
	{
		$_POST[str_replace("_"," ",$key)] = $value;
	}
	try {
		$contentLangId = $contentLangModel->Edit($_POST,$groups);
		$positionData = array();
		$positionData['Status'] = "Closed";
		$positionData['Character'] = $contentId;
		$success = $contentDataModel->EditAllContentData($positionData,$_POST['Position']*1,$groups);
	} catch(Exception $exception) {
		
	}
	return $contentId;	
}
function GetCharacter($id)
{
	$contentModel		= LoadModel(Common::LocalDB(),"Content","ContentModel");
	$character = $contentModel->Get($id);
	if ($character->Rank && $character->Rank > 0) 
	{
		$character->Rank = $contentModel->Get($character->Rank);
	} else {
		$character->Rank = null;
	}
	return $character;
}
function SimmDescription()
{
	return array(
		'Title' => "Simm Description",
		"Body" => "Bacon ipsum dolor amet alcatra porchetta hamburger capicola shoulder. Jerky turducken bresaola corned beef pancetta pig, turkey pastrami. Jowl ham hock tenderloin shoulder leberkas tongue turducken tri-tip, corned beef cow spare ribs. Shankle pork capicola, doner fatback alcatra pig beef ham hock cow chicken landjaeger. Fatback bresaola drumstick chicken."
	);
}

if (array_key_exists(1,$parameters)) {

	switch(strtolower($parameters[1]))
	{
		case "create":
			$contentModel=LoadModel(Common::LocalDB(),"Content","ContentModel");
			$ctfm = LoadModel(Common::LocalDB(),"Content","ContentTypeFieldsModel");
			
			$characterFields = $ctfm->GetContentTypeFieldsByName("Character");
			$postData 			= array();
			$additionalContent 	= array();
			$groups = (isset($_SESSION['user']->groups)?$_SESSION['user']->groups:array());
            
			foreach ($characterFields as $field)
			{
				$key 		= $field->Name;
				$inGet 		= array_key_exists($key,$_GET);
				$inPost 	= array_key_exists($key,$_POST);
				$value 		= $inPost?$_POST[$key]: ($inGet?$_GET[$key]:"");
				$postData[$field->Name] = $value;
                
				if ( strtolower($field->Type) == "content")
				{
                    
					$subData 								= $contentModel->GetContentByTypeId($field->TypeData,$groups);
					$additionalContent[$field->TypeData] 	= $subData;
				}
			}
			if ($isPost)
			{
				$id = AddCharacter();
				header("location:/simm/edit/{$id}");
			}
			$this->data['fields'] 			= $characterFields;
			$this->data['postData']			= $postData;
			$this->data['additionalData'] 	= $additionalContent;
			$this->data['selectedPosition'] = $_GET['position'] * 1;
			
			$contentView 					= new View($this->theme,$this->defaults,$this->data,$this->config);		
			$contentView 					= new View($this->theme,$this->defaults,$this->data,$this->config);
			echo $contentView->show('createCharacter.tpl.php');
		break;
		case "edit":
			if ( array_key_exists(2,$parameters) ) {
				$characterId = $parameters[2];
				$contentModel=LoadModel(Common::LocalDB(),"Content","ContentModel");
				$ctfm = LoadModel(Common::LocalDB(),"Content","ContentTypeFieldsModel");
				
				$characterFields = $ctfm->GetContentTypeFieldsByName("Character");
				$postData 			= array();
				$additionalContent 	= array();
				$groups = (isset($_SESSION['user']->groups)?$_SESSION['user']->groups:array());

				foreach ($characterFields as $field)
				{					
					if ( strtolower($field->Type) == "content")
					{						
						$subData 								= $contentModel->GetContentByTypeId($field->TypeData,$groups);
						$additionalContent[$field->TypeData] 	= $subData;
					}
				}
				if ($isPost)
				{
					$id = EditCharacter();
					if ($id) {
						header("location:/simm/edit/{$characterId}");
					}
				}
				$this->data['fields'] 				= $characterFields;
				$this->data['character']			= GetCharacter($characterId);
				$this->data['additionalData'] 	= $additionalContent;
				
				$contentView 					= new View($this->theme,$this->defaults,$this->data,$this->config);		
				$contentView 					= new View($this->theme,$this->defaults,$this->data,$this->config);
				echo $contentView->show('editCharacter.tpl.php');
			}
		break;
		case "view":
			if ( array_key_exists(2,$parameters) ) {
				
				$characterID 	= isset($parameters[2])?(int)$parameters[2]:0;
				$character 		= GetCharacter($characterID);
				$ctfModel		= LoadModel(Common::LocalDB(),"Content","ContentTypeFieldsModel");
				$fields= $ctfModel->GetContentTypeFieldsByName("Character");
				$this->AddData('character',$character);
				$this->AddData('fields',$fields);
				$this->config['page_title'] = "Viewing ".($character->ContentTitle?"{$character->ContentTitle}'s":"")." Profile";
				$contentView = new View($this->theme,$this->defaults,$this->data,$this->config);
				echo $contentView->show('character.tpl.php');
			}
		break;
		
	}
} else {
	$this->data['manifest'] = Manifest();
	$this->data['description'] = SimmDescription();
	$contentView = new View($this->theme,$this->defaults,$this->data,$this->config);		
	echo $contentView->show('simm.tpl.php');
}