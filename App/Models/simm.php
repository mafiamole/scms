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
	
		$positionGroupsData[$key]->Positions = $contentModel->GetChildItems($pg->ContentId,$groups);
		foreach ( $positionGroupsData[$key]->Positions as $key => $pos)
		{
			if ( isset($pos->Character) && $pos->Character && ($pos->Character*1) > 0 )
			{
				$character = $contentModel->Get($pos->Character*1);
				$positionGroupsData[$key]->Positions[$key]->Character = $character;
				$character->Rank = $contentModel->Get($character->Rank);
			}
		}
	}
	return $positionGroupsData;
}

function AddCharacter()
{
	// We need to filter the darta.
	$contentModel		= LoadModel(Common::LocalDB(),"Content","ContentModel");
	$contentLangModel 	= LoadModel(Common::LocalDB(),"Content","ContentLangModel");
	$ctm = LoadModel(Common::LocalDB(),"Content","ContentTypesModel");
	$contentDataModel = LoadModel(Common::LocalDB(),"Content","ContentDataModel");
	
	$characterType = $ctm->Get("Character");
	
	$_POST['ContentTypes_id'] = $characterType->Id;
	$_POST['Users_id'] = $_SESSION['user']->Id;
	$_POST['Applications_AppId'] =1;
	$contentId = $contentModel->Add($_POST);
	$_POST['Content_id'] = $contentId;
	$_POST['Languages_id'] = $_SESSION['user']->Languages_id;
	$_POST['Keywords'] = "";
	$_POST['Description'] = "";
	$contentLangId = $contentLangModel->Add($_POST);
	$ctfm = LoadModel(Common::LocalDB(),"Content","ContentTypeFieldsModel");
	$characterFields = $ctfm->GetContentTypeFieldsByName("Character");
	$contentDataModel->PopulateContentData($_POST,$characterType->Id,$contentLangId);
	
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
				AddCharacter();
				
			}
			$this->data['fields'] 			= $characterFields;
			$this->data['postData']			= $postData;
			$this->data['additionalData'] 	= $additionalContent;
			$contentView 					= new View($this->theme,$this->defaults,$this->data,$this->config);		
			$contentView 					= new View($this->theme,$this->defaults,$this->data,$this->config);		
			echo $contentView->show('createCharacter.tpl.php');
		break;
		
		
	}
} else {
	$this->data['manifest'] = Manifest();
	$this->data['description'] = SimmDescription();
	$contentView = new View($this->theme,$this->defaults,$this->data,$this->config);		
	echo $contentView->show('simm.tpl.php');
}