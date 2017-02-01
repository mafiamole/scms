<?php

$controller = new Controller('/simm',$this->data,$this->config,$this->theme);
$controller->AddModel("SimmsModels","Characters");
$controller->AddModel("SimmsModels","Simm");

$controller->Add
(
	REQUEST_POST,
	'/create',
	function($controller,$route,$parameters,$models)
	{
        if ( !empty($_POST) )
        {
			$characterId    	= $parameters[1]*1;
            $id = $models['Characters']-Add($_POST);
			if ( ($id * 1) > 0)
			{
				header("location:/simm/edit/{$characterId}");
			}
			else
			{
				$controller->AddData('errors',array('General'=>'Unable to add character'));
			}
        }		
	}
);

$controller->Add
(
	REQUEST_POST,
	'/edit/([0-9]*)',
	function($controller,$route,$parameters,$models)
	{
		if ( !empty($_POST) )
		{
			$characterId    	= $parameters[1]*1;
			$id = $models['Characters']->Edit($_POST);
			if ( ($id * 1) > 0)
			{
				header("location:/simm/edit/{$characterId}");
			}
			else
			{
				$controller->AddData('errors',array('General'=>'Unable to add character'));
			}
		}
	}
);

$controller->Add
(
	REQUEST_GET | REQUEST_POST,
	'/create',
	function($controller,$route,$parameters,$models)
	{
        $characterFields    = $characters->GetFields();
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

            if (strtolower($field->Type) == "content")
            {
                $subData 								= $contentModel->GetContentByTypeId($field->TypeData,$groups);
                $additionalContent[$field->TypeData] 	= $subData;
            }
        }
        $this->AddData('Fields',$characterFields);
        $this->AddData('PostData',$postData);
        $this->AddData('additionalData',$additionalContent);
        $this->AddData('SelectedPosition',$_GET['position'] * 1);
        
        $contentView = $controller->CreateView();
        echo $contentView->show('createCharacter.tpl.php');
	}
);

$controller->Add
(
	REQUEST_GET | REQUEST_POST,
	'/edit/([0-9]*)',
	function($controller,$route,$parameters,$models)
	{
		if ( array_key_exists(1,$parameters) )
        {
            $characterID    	= $parameters[1]*1;
			$character 			= $models['Characters']->Get($characterID);	
            $characterFields    = $models['Characters']->GetFields();
            $postData 			= array();
            $additionalContent 	= $models['Characters']->GetContentFieldOptions();
            $groups = (isset($_SESSION['user']->groups)?$_SESSION['user']->groups:array());

            $this->AddData('Fields',$characterFields);
            $this->AddData('Character',$character);
            $this->AddData('AdditionalData',$additionalContent);

            $contentView = $controller->CreateView();
            echo $contentView->show('editCharacter.tpl.php');
        }
	}
);

$controller->Add
(
	REQUEST_GET,
	'/view/([0-9]+)',
	function($controller,$route,$parameters,$models)
	{
		
        if ( array_key_exists(1,$parameters) )
        {
            $characterID 	= $parameters[1]*1;
            $character 		= $models['Characters']->Get($characterID);
            $ctfModel		= LoadModel(Common::LocalDB(),"Content","ContentTypeFieldsModel");
            $fields         = $models['Characters']->GetFields();
            $this->AddData('Character',$character);
            $this->AddData('Fields',$fields);
            $this->AddConfig('page_title',"Viewing ".($character->ContentTitle?"{$character->ContentTitle}'s":"")." Profile");
            $contentView = $controller->CreateView();
			$contentView->AddConfig('page_title',"Viewing ".($character->ContentTitle?"{$character->ContentTitle}'s":"")." Profile");
            echo $contentView->show('character.tpl.php');
        }
	}
);

/** Default Route **/
$controller->Add
(
	REQUEST_GET,
	'/?$',
	function($controller,$route,$parameters,$models)
	{
		$manifest 		= $models['Simm']->Manifest();
		$description 	= $models['Simm']->Description();
        $controller->AddData('Manifest',$manifest);
        $controller->AddData('Description',$description);
		
        $contentView = $controller->CreateView();
        echo $contentView->show('simm.tpl.php');
	}
);
$controller->Run($_SERVER['REQUEST_URI'],CheckRequestMethod());
