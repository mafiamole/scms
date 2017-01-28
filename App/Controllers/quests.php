<?php

$parameter1 = $parameters(1);
$parameter2 = $parameters(2);
function AddQuest()
{
	
}
$stories    = LoadModel(Common::LocalDB(),"SimmsModels","Stories");
$ranks      = LoadModel(Common::LocalDB(),"SimmsModels","Ranks");
$characters = LoadModel(Common::LocalDB(),"SimmsModels","Characters");
function GetQuests()
{
	$groups = GetGroups();
	$contentModel		= LoadModel(Common::LocalDB(),"Content","ContentModel");
	$questCats 			= $contentModel->GetContentByType("QuestCategory",$groups);
	foreach ($questCats as $key => $questCat)
	{
		$questCats[$key]->Quests = $contentModel->GetChildItems($questCat->ContentId,$groups);
		foreach ($questCats[$key]->Quests as $QKey => $quest)
		{
			$questCats[$key]->Quests[$QKey]->Posts = $contentModel->GetChildItems($quest->ContentId,$groups); // Change to a count!
		}
	}
	return $questCats;
}

function GetRanks()
{
	$groups 		= GetGroups();
	$contentModel	= LoadModel(Common::LocalDB(),"Content","ContentModel");
	$contents 		= $contentModel->GetContentByType("Rank",$groups);
	return $contents;
}

function GetQuest($id)
{
	$groups            = GetGroups();
	$contentModel		= LoadModel(Common::LocalDB(),"Content","ContentModel");
	$content = $contentModel->Get($id);
	$content->Posts =  $contentModel->GetChildItems($content->ContentId,$groups);
	$ranks = GetRanks();
	$characters = GetCharacters(true);
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
					//$foundChar->Rank = FindContent($foundChar->Rank,$ranks);
					$content->Posts[$key]->Characters[] = $foundChar;
					
				}
			}
		}
		else
		{
			$post->Characters = array();
		}
				
	}
	return $content;
}

function GetCharacters($getRanks = false)
{
	$groups = GetGroups();
	$contentModel		= LoadModel(Common::LocalDB(),"Content","ContentModel");
	$contents = $contentModel->GetContentByType("Character",$groups);
	$ranks = GetRanks();
	if ( $getRanks ) {
		foreach ($contents as $key => $c)
		{
			$c->Rank = new stdclass();
			$c->Rank = FindContent($c->Rank,$ranks);
			$contents[$key] = $c;
		}
	}
	return $contents;
}

function AddPost($post,$questId)
{
	$contentModel		= LoadModel(Common::LocalDB(),"Content","ContentModel");
	$contentLangModel	= LoadModel(Common::LocalDB(),"Content","ContentLangModel");
	$ctm 				= LoadModel(Common::LocalDB(),"Content","ContentTypesModel");
	$contentDataModel 	= LoadModel(Common::LocalDB(),"Content","ContentDataModel");
	
	$quest 			= $contentModel->Get($questId);
	$contentType	= $ctm->Get("QuestPost");
	if ($quest) {
		$post['Parent_id'] 			= $questId * 1;
		$post['URL']				= "/quests/view/";
		
		$post['Users_id'] 			= $_SESSION['user']->Id;
		$post['ContentTypes_id'] 	= $contentType->Id;
		$post['Applications_AppId'] = 1;
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
		$contentId = $contentModel->Add($post);
		$post['Content_id'] = $contentId;
		$contentLangId = $contentLangModel->Add($post);
		header("location:/quests/view/".$questId);
	}
}

$view = null;
switch ( $function ) {
	case "create":		
		$this->config['page_title'] = "Create a new Quest";
		$contentView = new View($this->theme,$this->defaults,$this->data,$this->config);		
		echo $contentView->show('createQuest.tpl.php');
	break;
	case "post":		
		$this->config['page_title'] = "Create a new Quest Post";
		$qID = isset($parameters[2])?(int)$parameters[2]:0;
		$characters = GetCharacters();
		$this->data->Add('UsersCharacters',$characters);
		$this->data->Add('OthersCharacters',$characters);
		$this->data->Add('PostData', array(
			'Title' => "",
			'Description' => "",
			'UsersCharacters' => array(),
			'OthersCharacters' => array()
			));
		;
        $postErr = array();
		if (isset($_POST) && !empty($_POST))
		{			
			if ( isset($_POST['Title']) ) {
				$this->data['PostData']['Title'] = strip_tags($_POST['Title']);
			}
			
			if ( isset($_POST['Description']) ) 
			{
				$body = strip_tags($_POST['Description']);
				if ( strlen($body) <= 0 )
				{
					$err['Description'] = 'Please enter the body of your post';
				}
				$this->data['PostData']['Description'] = $body;
			} else {
				$postErr['Description'] ='Description not found';
			}
			if ( isset($_POST['UsersCharacters']) && count($_POST['UsersCharacters']) > 0  ) {
				foreach ( $_POST['UsersCharacters'] as $character ) 
				{
					$this->data['PostData']['UsersCharacters'][] = $character * 1;
				}
			}
            else
            {
				$postErr['UsersCharacters'] = 'No user characters selected';
			}
			if ( isset($_POST['OthersCharacters']) && count($_POST['OthersCharacters']) > 0  )
            {
				foreach ( $_POST['OthersCharacters'] as $character ) 
				{
					$this->data['PostData']['OthersCharacters'][] = $character * 1;
				}				
            }
            $success = $stories->Add($this->Data['PostData'],$qID)
			if ( $success ) {
				header("locations:/quests/view/".$qID);
			}			
		}
		$this->errors->Add('Post',$postErr);
		$contentView = new View($this->theme,$this->defaults,$this->data,$this->config);		
		echo $contentView->show('createQuestPost.tpl.php');
	break;	
	case "view":		
		$contentView = new View($this->theme,$this->defaults,$this->data,$this->config);
		$qID = $parameter2;
		$quest = $stories->GetCategories($qID);
		$this->data['Quest'] = $quest;
		if ($quest != null)
        {			
			$this->config['page_title'] = "Viewing ".$quest->ContentTitle;
		}
		$contentView = new View($this->theme,$this->defaults,$this->data,$this->config);
		echo $contentView->show('quest.tpl.php');		
	break;
	default:
		$config['page_title'] = "Quests";
		$this->data['QuestCategories'] = $stories->GetAll();
        
		$contentView = new View($this->theme,$this->defaults,$this->data,$this->config);
		echo $contentView->show('quests.tpl.php');
	break;	
}
