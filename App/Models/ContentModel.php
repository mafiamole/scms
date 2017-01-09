<?php
namespace /models;

public ContentModel implements Model
{
	protected $db;
	protected $dbHelper;
	
	public function __construct($db)
	{
		$this->db 		= $db;
		$this->dbHelper = new MySQLModelHelper($db,"Content",array(
			'Id','Parent_id','URL','ContentTypes_id','Users_id','Applications_AppId'
		));
	}
	public function Add($data)
	{
		$this->dbHelper->Add($data,array('Parent_id','URL','ContentTypes_id','Users_id','Applications_AppId'));
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
	// Will return the content item if the user has access to it
	public function GetContentByUser($contentId,$userId)
	{
		$query = "SELECT * FROM `View_LanguageContent` WHERE `ContentId` = :contentId AND `UserId` = :userId";
		$prep = $this->db->prepare($query);
		$prep->execute(array(':contentId'=>$contentId,':userId'=>$userId));
		$allContent = $prep->fetchAll();
		$content = $allContent[0]
		if (!$content) throw new Exception("Access Denied");
		
		$contentDataModel = new ContentDataModel($this->db);
		
		$groupIds = array();
		
		foreach($allContent as $content) $groupIds[] = $content['UserGroupId'];
		
		$contentData = $contentDataModel->GetContentDataByContentLang($content['ContentLangId'],array($groupIds));
		
		foreach ($contentData as $cd) {
			if ( !array_key_exists($cd['Name'],$content) ) {
				$content[$cd['Name']] = $cd;
			}
		}
		return $content;
	}
	public function GetContentByType($typeName,$userId)
	{
		$query = "SELECT * FROM `View_LanguageContent` WHERE `TypeName` = :typeName AND `UserId` = :userId";
		$prep = $this->db->prepare($query);
		$prep->execute(array(':typeName'=>$typeName,':userId'=>$userId));
		$allContent = $prep->fetchAll();
		$content = $allContent[0]
		if (!$content) throw new Exception("Access Denied");
		
		$contentDataModel = new ContentDataModel($this->db);
		
		$groupIds = array();
		
		foreach($allContent as $content) $groupIds[] = $content['UserGroupId'];
		
		$contentData = $contentDataModel->GetContentDataByContentLang($content['ContentLangId'],array($groupIds));
		
		foreach ($contentData as $cd) {
			if ( !array_key_exists($cd['Name'],$content) ) {
				$content[$cd['Name']] = $cd;
			}
		}
		return $allContent;
	}
	public function Search($parameters)
	{
		
	}
}