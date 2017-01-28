<?php

$parameter1 = $parameters->Get(1);
$parameter2 = $parameters->Get(2);

$isPost     = (isset($_POST) && !empty($_POST));
$characters = LoadModel($this->db,"SimmsModels","Characters");
$simm       = LoadModel($this->db,"SimmsModels","Simm");

switch($parameter1)
{
    case "create":
        

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

            if ( strtolower($field->Type) == "content")
            {

                $subData 								= $contentModel->GetContentByTypeId($field->TypeData,$groups);
                $additionalContent[$field->TypeData] 	= $subData;
            }
        }
        if ($isPost)
        {
            $id = $characters->Add($_POST);
            header("location:/simm/edit/{$id}");
        }
        $this->AddData('Fields',$characterFields);
        $this->AddData('PostData',$postData);
        $this->AddData('additionalData',$additionalContent);
        $this->AddData('SelectedPosition',$_GET['position'] * 1);
        
        $contentView 					= new View($this->theme,$this->defaults,$this->data,$this->config);
        echo $contentView->show('createCharacter.tpl.php');
    break;
    case "edit":
        if ( $parameter2 )
        {
            $characterId    = $parameter2*1;

            $characterFields    = $characters->GetFields();
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
                $id = $characters->Add($_POST);
                if ($id) {
                    header("location:/simm/edit/{$characterId}");
                }
            }
            $this->AddData('Fields',$characterFields);
            $this->AddData('Character',GetCharacter($characterId));
            $this->AddData('AdditionalData',$additionalContent);

            $contentView 					= new View($this->theme,$this->defaults,$this->data,$this->config);
            echo $contentView->show('editCharacter.tpl.php');
        }
    break;
    case "view":
        if ( $parameter2 )
        {
            $characterID 	= $parameter2*1;
            $character 		= $characters->Get($characterID);
            $ctfModel		= LoadModel(Common::LocalDB(),"Content","ContentTypeFieldsModel");
            $fields         = $characters->GetFields();
            $this->AddData('Character',$character);
            $this->AddData('Fields',$fields);
            $this->AddConfig('page_title',"Viewing ".($character->ContentTitle?"{$character->ContentTitle}'s":"")." Profile");
            $contentView = new View($this->theme,$this->defaults,$this->data,$this->config);
            echo $contentView->show('character.tpl.php');
        }
    break;
    default:
        $this->AddData('Manifest',$simm->Manifest());
        $this->AddData('Description',$simm->Description());	
        $contentView = new View($this->theme,$this->defaults,$this->data,$this->config);		
        echo $contentView->show('simm.tpl.php');            
    break;
}
