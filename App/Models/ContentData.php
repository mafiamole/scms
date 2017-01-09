<?php
namespace /models;

public ContentDataModel implements Model
{
	protected $db;
	protected $dbHelper;
	
	public function __construct($db)
	{
		$this->db 		= $db;
		$this->dbHelper = new MySQLModelHelper($db,"ContentData",array(
			'Id','ContentLang_id','ContentTypeFields_id','Value'
		));
	}
	public function Add($data)
	{
		$this->dbHelper->Add($data,array('Content_id','ContentLang_id','ContentTypeFields_id','Value'));
	}
	public function Edit($data)
	{
		$this->dbHelper->Edit($data);
	}
	public function Delete($id,$chain = false)
	{
		return $this->dbHelper->Delete($id);
	}
	public function Get($id)
	{
		return $this->dbHelper->Get(array('id'=>$id),"`Id` = :id");
	}
	public function GetAll()
	{
		return $this->dbHelper->GetAll();	
	}
	public function GetContentDataByGroups($contentLangId,$groupIds)
	{
		$placeHolders = array();
		$prepData = array();
		$prepData[':contentLangId'] = $contentLangId;
		foreach ($groupIds as $key => $groupId)
		{
			$placeHolder = ":group_".$key;
			$placeHolders[] = $placeholder;
			$prepData[$placeholder] = $groupId;
		}
		$placeHolderStr = implode(",",$placeHolders);
		$query = "SELECT * FROM `View_LanguageContentData` WHERE `ContentLang`.`Id` = :contentLangId AND `UserGroup` IN ($placeHolderStr)";
		$prep = $this->db->prepare($query);
		$prep->execute($prepData);
		return $prep->fetchAll();
	}
	public function Search($parameters)
	{
		
	}
}