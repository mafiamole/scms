<?php

$this->AddModel("Characters");
$this->AddModel("Simm");
$this->AddModel("Content");

$this->Add
(
    REQUEST_POST,
    '/create',
    function($controller,$route,$parameters,$models)
    {
        
        if ( !empty($_POST) )
        {
            
            $id = $models['Characters']->Add($_POST);
            if ( ($id * 1) > 0)
            {
                header("location:/simm/edit/{$id}");
            }
            else
            {
                $this->AddData('Errors',array('General'=>'Unable to add character'));
            }
        }		
    }
);

$this->Add
(
    REQUEST_POST,
    '/edit/([0-9]*)',
    function($controller,$route,$parameters,$models)
    {
        if ( !empty($_POST) )
        {
            $characterId = $parameters[1]*1;
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

$this->Add
(
    REQUEST_GET | REQUEST_POST,
    '/create',
    function($controller,$route,$parameters,$models)
    {
        $characterFields    = $this->models['Characters']->GetFields();
        $postData           = array();
        $additionalContent  = array();
        $groups             = (isset($_SESSION['user']->groups)?$_SESSION['user']->groups:array());
        $selectedPosition   = (array_key_exists('position',$_GET)?$_GET['position']*1:0);
        $postData['Title']  = isset($_POST['Title'])?$_POST['Title']:"";
        foreach ($characterFields as $field)
        {
            $key                    = $field->Name;
            $inGet                  = array_key_exists($key,$_GET);
            $inPost                 = array_key_exists($key,$_POST);
            $value                  = $inPost?$_POST[$key]: ($inGet?$_GET[$key]:"");
            $postData[$field->Name] = $value;

            if (strtolower($field->Type) == "content")
            {
                $subData                             = $this->models['ContentModel']->GetContentByTypeId($field->TypeData,$groups);
                $additionalContent[$field->TypeData] = $subData;
            }
        }
        $this->AddData('Fields',$characterFields);
        $this->AddData('PostData',$postData);
        $this->AddData('AdditionalData',$additionalContent);
        $this->AddData('SelectedPosition',$selectedPosition);

        $this->ShowView('createCharacter');
    }
);

$this->Add
(
    REQUEST_GET | REQUEST_POST,
    '/edit/([0-9]*)',
    function($controller,$route,$parameters,$models)
    {
        if ( array_key_exists(1,$parameters) )
        {
            $characterID    	= $parameters[1]*1;
            $character 		= $models['Characters']->Get($characterID);	
            $characterFields    = $models['Characters']->GetFields();
            $postData 		= array();
            $additionalContent 	= $models['Characters']->GetContentFieldOptions();
            $groups             = (isset($_SESSION['user']->groups)?$_SESSION['user']->groups:array());

            $this->AddData('Fields',$characterFields);
            $this->AddData('Character',$character);
            $this->AddData('AdditionalData',$additionalContent);

            $this->ShowView('editCharacter');
        }
        }
);

$this->Add
(
    REQUEST_GET,
    '/view/([0-9]+)',
    function($controller,$route,$parameters,$models)
    {
        if ( array_key_exists(1,$parameters) )
        {
            $characterID    = $parameters[1]*1;
            $character      = $models['Characters']->Get($characterID);
            $ctfModel       = LoadModel(Common::LocalDB(),"Content","ContentTypeFieldsModel");
            $fields         = $models['Characters']->GetFields();
            $this->AddData('Character',$character);
            $this->AddData('Fields',$fields);
            $this->AddData('page_title',"Viewing ".($character->ContentTitle?"{$character->ContentTitle}'s":"")." Profile");
            $this->ShowView('character');
        }
    }
);

/** Default Route **/
$this->Add
(
    REQUEST_GET,
    '/?$',
    function($controller,$route,$parameters,$models)
    {
        $manifest 	= $models['Simm']->Manifest();
        $description 	= $models['Simm']->Description();
        $this->AddData('Manifest',$manifest);
        $this->AddData('Description',$description);
        $this->ShowView('simm');
    }
);
