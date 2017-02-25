<?php

$parameter1 = $parameters->Get(1);
$parameter2 = $parameters->Get(2);

$contentModel=LoadModel(Common::LocalDB(),"Content");

switch ($parameter1)
{
    case "create":		
        $this->config['page_title'] = "Create a new Character";
        $contentView = new View($this->theme,$this->defaults,$this->data,$this->config);		
        echo $contentView->show('createCharacter.tpl.php');
    break;
    case "view":
        $this->config['page_title'] = "Viewing Profile";
        $characterID = $parameter2;

        if (array_key_exists($characterID,$characters))
        {
                $this->data['Character'] = $characters[$characterID];
                $this->config['page_title'] = $characters[$characterID]['name'] . "'s Profile";
        } else {
                $contentView->AddData('Character',null);
        }
        $contentView = new View($this->theme,$this->defaults,$this->data,$this->config);
        echo $contentView->show('character.tpl.php');		
    break;
    default:		
        $this->config['page_title'] = "Characters";
        $this->data['Headers'] = $characterFields;
        $this->data['Characters'] = $characters;
        $contentView = new View($this->theme,$this->defaults,$this->data,$this->config);
        echo $contentView->show('characters.tpl.php');
    break;	
}