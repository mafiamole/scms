<?php

Class Characters
{
	private $db;
	private $name;
	private $charModel;
	private $charLangModel;
	private $ctm;
	private $ctfm;
	
	public function __Construct($db,$name="Character")
	{
	$this->charModel		= LoadModel(Common::LocalDB(),"Content","ContentModel");
	$this->charLangModel 	= LoadModel(Common::LocalDB(),"Content","ContentLangModel");
	$this->ctm 				= LoadModel(Common::LocalDB(),"Content","ContentTypesModel");
	$this->contentDataModel = LoadModel(Common::LocalDB(),"Content","ContentDataModel");
	$this->ctfm = LoadModel(Common::LocalDB(),"Content","ContentTypeFieldsModel");
	}
	public function Get()
	{
		
	}
	public function GetAll()
	{
	if (!$_SESSION['user']->Id)
	{
		return false;
	}
	$groups = GetGroups();
	
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
	public function Add()
	{
		
	}
	public function Edit()
	{
		
	}
	
}