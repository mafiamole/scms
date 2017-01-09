<?php
if ( count($parameters) > 1 ) {
	$function = $parameters[1];
} else {
	$function = "";
}
$characterFields=array(
			'group'		=> "Group",
			'position'	=> "Role",
			'rank'		=> "Rank",
			'name'		=> "Name",
			'view'		=> "View"
		);
		
require_once(APP_FOLDER . "testCharacterList.php");
$contentModel=LoadModel(Common::LocalDB(),"Content","ContentModel");
switch ( $function ) {
	case "create":
		
		$this->config['page_title'] = "Create a new Character";
		$contentView = new View($this->theme,$this->defaults,$this->data,$this->config);		
		echo $contentView->show('createCharacter.tpl.php');
	break;
	case "view":
		$contentView = new View($this->theme,$this->defaults,$this->data,$this->config);
		$this->config['page_title'] = "Viewing Profile";
		$characterID = isset($parameters[2])?(int)$parameters[2]-1:0;

		if (array_key_exists($characterID,$characters))
		{
			$this->data['character'] = $characters[$characterID];
			$this->config['page_title'] = $characters[$characterID]['name'] . "'s Profile";
		} else {
			$contentView->AddData('character',null);
		}
		$contentView = new View($this->theme,$this->defaults,$this->data,$this->config);
		echo $contentView->show('character.tpl.php');		
	break;
	default:
		
		$this->config['page_title'] = "Characters";
		$this->data['headers'] = $characterFields;
		$this->data['characters'] = $characters;
		$contentView = new View($this->theme,$this->defaults,$this->data,$this->config);
		echo $contentView->show('characters.tpl.php');
	break;	
}