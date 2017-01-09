<?php

namespace models;

class ContentTypesModel implements \IModel
{
	protected $db;
	protected $dbHelper;
	
	public function __construct($db)
	{
		$this->db 		= $db;
		$this->dbHelper = new \MySQLModelHelper($db,"contenttypes",array(
			'Id','Name'
		));
	}
	public function Add($data)
	{
		return $this->dbHelper->Add($data,array('Name'));
	}
	public function Edit($data)
	{
		return $this->dbHelper->Edit($data);
	}
	public function Delete($id,$chain = false)
	{
		return $this->dbHelper->Delete($id);
	}
	public function Get($id)
	{
		if ( is_integer($id) ) {
			return $this->dbHelper->Get(array('id'=>$id),"`Id` = :id");
		} else {
			return $this->dbHelper->Get(array('Name'=>$id),"`Name` = :Name");
		}
	}
	public function GetContentTypesFields($contentType)
	{
		$ctfModel = new ContentTypeFieldsModel($this->db);
		return $ctfModel->GetContentTypesFields($contentType);
	}
	public function GetAll()
	{
		return $this->dbHelper->GetAll();	
	}
	public function Search($parameters)
	{
		
	}
}

class ContentTypeFieldsModel implements \IModel
{
	protected $db;
	protected $dbHelper;
	
	public function __construct($db)
	{
		$this->db 		= $db;
		$this->dbHelper = new \MySQLModelHelper($db,"contenttypefields",array(
			'Id','ContentType_id','Name','Type','TypeData'
		));
	}
	public function Add($data)
	{
		return $this->dbHelper->Add($data,array('ContentType_id','Name','Type'));
	}
	public function Edit($data)
	{
		return $this->dbHelper->Edit($data);
	}
	public function Delete($id,$chain = false)
	{
		return $this->dbHelper->Delete($id);
	}
	public function Get($id)
	{
		return $this->dbHelper->Get(array('id'=>$id),"`Id` = :id");
	}
	public function GetContentTypesFields($contentType)
	{
		return $this->dbHelper->GetAll(array('contentType'=>$contentType),"`ContentTypes_id`=:contentType");	
	}
	public function GetContentTypeFieldsByName($contentTypeName)
	{
		$prep = $this->db->prepare("SELECT `Id`,`Name`,`Type`,`TypeData` FROM `view_contenttypefields` WHERE contenttypes_name = :contentTypeName");
		$success = $prep->execute(array(':contentTypeName'=>$contentTypeName));
		return $prep->FetchAll(\PDO::FETCH_OBJ);
	}
	public function GetAll()
	{
		return $this->dbHelper->GetAll();	
	}
	public function Search($parameters)
	{
		
	}
}

class ContentDataModel implements \IModel
{
	protected $db;
	protected $dbHelper;
	
	public function __construct($db)
	{
		$this->db 		= $db;
		$this->dbHelper = new \MySQLModelHelper($db,"contentdata",array(
			'Id','ContentLang_id','ContentTypeFields_id','Value'
		));
	}
	public function Add($data)
	{
		$clID 	= $data['ContentTypeFields_id'];
		$ctfID 	= $data['ContentLang_id'];	
		$ctfModel = new ContentTypeFieldsModel($this->db);
		$field = $ctfModel->Get($clID);		
		$fieldType = strtoupper($field->Type);
		$fileTypes = array("IMAGE","FILE");
		if ( in_array($fieldType,$fileTypes) )
		{
			$fileName = FileUpload("/Resources/Uploads/Content/{$ctfID}/",$field->Name,$field->Type);
			$data['Value'] = $fileName;
		}		
		$success = $this->dbHelper->Add($data,array('ContentLang_id','ContentTypeFields_id','Value'));

		return $success;
	}
	public function Edit($data)
	{
		$id = $data['Id'];
		$clID = 0;
		$ctfID = 0;
		if ( !array_key_exists("ContentLang_id",$data) || !array_key_exists("ContentTypeFields_id",$data) ) {
			$existingContent = $this->Get($id);
			$clID 	= $existingContent->ContentTypeFields_id;
			$ctfID 	= $existingContent->ContentLang_id;
		} else {
			$clID 	= $data['ContentTypeFields_id'];
			$ctfID 	= $data['ContentLang_id'];			
		}
		
		$ctfModel = new ContentTypeFieldsModel($this->db);
		$field = $ctfModel->Get($clID);
		$fieldType = strtoupper($field->Type);
		$fileTypes = array("IMAGE","FILE");
		if ( in_array($fieldType,$fileTypes) )
		{
			$fileName = FileUpload("/Resources/Uploads/Content/{$ctfID}/",$field->Name,$field->Type);
			$data['Value'] = $fileName;
		}
		$success = $this->dbHelper->Edit($data,"`Id` = :Id");
		
		return $success;
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
	public function GetContentDataByGroups($contentId,$groupIds)
	{
		$placeHolders = array();
		$prepData = array();
		$prepData[':contentId'] = $contentLangId;
		foreach ($groupIds as $key => $groupId)
		{
			$placeHolder = ":group_".$key;
			$placeHolders[] = $placeholder;
			$prepData[$placeholder] = $groupId;
		}
		$placeHolderStr = implode(",",$placeHolders);
		$query = "SELECT * FROM `view_languagecontentdata` WHERE `ContentId`.`Id` = :contentId AND `UserGroup` IN ($placeHolderStr)";
		$prep = $this->db->prepare($query);
		$prep->execute($prepData);
		return $prep->fetchAll(\PDO::FETCH_OBJ);
	}
	public function GetContentDataByContentId($contentId,$userGroups) {
		$ugs = array();
		$data = array();
		foreach ($userGroups as $userGroup)
		{
			if (  is_numeric($userGroup) ) {
				$key = ":usergroup{$userGroup}";
				$data[$key] = $userGroup;
			} else {
				$key = ":usergroup{$userGroup->Id}";
				$data[$key] = $userGroup->Id;
			}
			
			$ugs[] = $key;
		}
		$userGroupsIn = implode(",",$ugs);
		$data[':contentId'] = $contentId;
		$query = "SELECT * FROM `view_languagecontentdata` WHERE `ContentId`=:contentId and `UserGroup` IN ($userGroupsIn)";
		$prep = $this->db->prepare($query);
		$success = $prep->execute($data);

		if (!$success) {
			Debug($prep->errorCode());
		}
		return $prep->fetchAll(\PDO::FETCH_OBJ);
	}	
	public function PopulateContentData($data,$contentTypeId,$contentLangId)
	{
		$ctm = new ContentTypesModel($this->db);
		$contentTypeFields = $ctm->GetContentTypesFields($contentTypeId);
		foreach($contentTypeFields as $field) {
			$key = $field->Name;
			$value = (array_key_exists($key,$data)?$data[$key]:""); // {populate with empty value for now;
			$populateData = array(
				'ContentLang_id' => $contentLangId,
				'ContentTypeFields_id'=> $field->id,
				'Value' => $value
			);
			$this->Add($populateData);
		}
		
	}
	public function EditAllContentData($data,$contentId,$groups)
	{
		$items = $this->GetContentDataByContentId($contentId,$groups);
		foreach($items as $item)
		{
			$altKey = str_replace(" ","_",$item->Name);
			if ( array_key_exists($item->Name,$data) || array_key_exists($altKey,$data) )
			{ 
				$value = $data[$item->Name];
				$newData = array(
					'Id' => $item->DataId,
					'Value' => $value
				);
				$success = $this->Edit($newData);
			}
			if ( array_key_exists($altKey,$_FILES) ){
				$value = $_FILES[$altKey];
				$newData = array(
					'Id' => $item->DataId,
					'Value' => $value
				);
				$success = $this->Edit($newData);				
			}
		}
	}
	public function Search($parameters)
	{
		
	}
}

class ContentLangModel implements \IModel
{
	protected $db;
	protected $dbHelper;
	
	public function __construct($db)
	{
		$this->db 		= $db;
		$this->dbHelper = new \MySQLModelHelper($db,"contentlang",array(
			'Id','Content_id','Languages_id','Title','Keywords','Description','Created','LastModified'
		));
	}
	public function Add($data)
	{
		if (!isset($data['Created']) )
		{
			$data['Created'] = date('c');
		}
		$addId = $this->dbHelper->Add($data,array('Content_id','Languages_id','Title','Keywords','Description','Created'));
		// Create the content lang and other fields
		$ctModel = new ContentDataModel($this->db);

		$ctModel->PopulateContentData($data,$data['Content_id'],$addId);		
		return $addId;
	}
	public function Edit($data)
	{
		$groups = GetGroups();
		if ( !isset($data['LastModified']) )
		{
			$data['LastModified'] = date('c');
		}
		$success = $this->dbHelper->Edit($data,"`Id` = :Id");
		$ctModel = new ContentDataModel($this->db);
		$contentId = 0;
		if (!array_key_exists('ContentId',$data)) {
			$newData = $this->Get($data['Id']);
			
			$contentId = $data['Content_id'];
		} else {
			$contentId =$data['ContentId'];
		}
		if ($contentId > 0) {
			$ctModel->EditAllContentData($data,$data['Content_id'],$groups);	
		}
		return $success;
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
	public function Search($parameters)
	{
		
	}
}

class ContentModel implements \IModel
{
	protected $db;
	protected $dbHelper;

	public function __construct($db)
	{
		$this->db 		= $db;
		$this->dbHelper = new \MySQLModelHelper($db,"content",array(
			'Id','Parent_id','URL','ContentTypes_id','Users_id','Applications_AppId'
		));
	}
	public function Add($data)
	{
		$id = $this->dbHelper->Add($data,array('URL','ContentTypes_id','Users_id','Applications_AppId'));
		return $id;
	}
	public function Edit($data)
	{
		return $this->dbHelper->Edit($data,"`Id` = :Id");
	}
	public function Delete($id,$chain = false)
	{
		return $this->dbHelper->Delete($id);
	}
	public function Get($id)
	{
		$data = array();
		$ugs = array();
		$groups = GetGroups();
		foreach ($groups as $userGroup)
		{
			if (  is_numeric($userGroup) ) {
				$key = ":usergroup{$userGroup}";
				$data[$key] = $userGroup;
			} else {
				$key = ":usergroup{$userGroup->Id}";
				$data[$key] = $userGroup->Id;
			}
			
			$ugs[] = $key;
		}
		$placeHolderStr = implode(",",$ugs);
		$data[':contentId'] = $id;
		$query = "SELECT * FROM `view_languagecontent` WHERE `ContentId` = :contentId AND `UserGroupId` IN ($placeHolderStr)";
		$prep = $this->db->prepare($query);
		$prep->execute($data);
		$content = $prep->fetch(\PDO::FETCH_OBJ);
		if (!$content) {
            Debug($id);
			$errInfo = $prep->errorInfo();
			if ( $errInfo[0] != "00000" ) {
				Debug($prep->errorInfo());
			}
			throw new \Exception("Access Denied"); 
        } 
		
		$contentDataModel = new ContentDataModel($this->db);
		
		$groupIds = array($content->UserGroupId);
				
		$contentData = $contentDataModel->GetContentDataByContentId($content->ContentId,$groupIds);
		
		foreach ($contentData as $cd) {
			if ( !array_key_exists($cd->Name,$content) ) {
				$key = $cd->Name;
				$content->$key = $cd->Content;
			}
		}
		return $content;
	}
	public function GetAll()
	{
		return $this->dbHelper->GetAll();
	}	
	// Will return the content item if the user has access to it
	public function GetContentByUser($contentId,$userId)
	{
		$query = "SELECT * FROM `view_languagecontent` WHERE `ContentId` = :contentId AND `UserId` = :userId ORDER BY `UserId` DESC";
		$prep = $this->db->prepare($query);
		$prep->execute(array(':contentId'=>$contentId,':userId'=>$userId));
		$allContent = $prep->fetchAll(\PDO::FETCH_OBJ);
		$content = $allContent[0];
		if (!$content) throw new Exception("Access Denied");
		
		$contentDataModel = new ContentDataModel($this->db);
		
		$groupIds = array();
		
		foreach($allContent as $content) $groupIds[] = $content['UserGroupId'];
		
		$contentData = $contentDataModel->GetContentDataByContentId($content->ContentId,$groupIds);
		
		foreach ($contentData as $cd) {
			if ( !array_key_exists($cd['Name'],$content) ) {
				$content[$cd['Name']] = $cd->Content;
			}
		}
		return $content;
	}
	/**
	 * TODO: $subType
	 *
	 */
	public function GetChildItems($parentId,$groups,$subType = null)
	{
		$data = array();
		$groupsPHs = array();
		
		foreach ($groups as $key => $group)
		{
			$ph = ":group{$group->Id}";
			$data[$ph] = $group->Id;
			$groupsPHs[] = $ph;
		}
		$groupIdsIns = 'IN ('.implode(',',$groupsPHs).')';
		$query = "SELECT * FROM `view_languagecontent` WHERE `Parent` = :parent AND `UserGroupId` {$groupIdsIns} GROUP BY `ContentId` ORDER BY `UserId` DESC";
		$prep = $this->db->prepare($query);
		$data[':parent'] = $parentId;

		$prep->execute($data);
		$allContent = $prep->fetchAll(\PDO::FETCH_OBJ);
		if (!empty($allContent))
		{
			$content = $allContent[0];
			if (!$allContent || empty($allContent)) throw new Exception("Access Denied");
			
			$contentDataModel = new ContentDataModel($this->db);
			
			$groupIds = array();
			
			foreach($groups as $g) $groupIds[] = $g->Id;
			
			foreach ( $allContent as $key => $content)
			{
				$contentData = $contentDataModel->GetContentDataByContentId($content->ContentId,$groupIds);
				foreach ($contentData as $cd)
				{
					if ( !array_key_exists($cd->Name,$content) )
					{
						$keyName = $cd->Name;
						$allContent[$key]->$keyName = $cd->Content;
					}
				}
			}
		}
		return $allContent;		
	}
	public function GetContentByTypeId($id,$groups)
	{
		$data = array();
		$groupsPHs = array();
		
		foreach ($groups as $key => $group) {
			$ph = ":group{$group->Id}";
			$data[$ph] = $group->Id;
			$groupsPHs[] = $ph;
		}
		$groupIdsIns = 'IN ('.implode(',',$groupsPHs).')';
		$query = "SELECT * FROM `view_languagecontent` WHERE `TypeId` = :typeName AND `UserGroupId` {$groupIdsIns} GROUP BY `ContentId` ORDER BY `UserId` DESC";
		$prep = $this->db->prepare($query);
		$data[':typeName'] = $id;

		$prep->execute($data);
		$allContent = $prep->fetchAll(\PDO::FETCH_OBJ);
		if (!empty($allContent))
		{
			$content = $allContent[0];
			if (!$allContent || empty($allContent)) throw new Exception("Access Denied");
			
			$contentDataModel = new ContentDataModel($this->db);
			
			$groupIds = array();
			
			foreach($allContent as $c) $groupIds[] = $c->UserGroupId;
			
			foreach ( $allContent as $key => $content)
			{
				$contentData = $contentDataModel->GetContentDataByContentId($content->ContentId,$groupIds);

				foreach ($contentData as $cd)
				{
					if ( !array_key_exists($cd->Name,$content) )
					{
						$keyName = $cd->Name;
						$allContent[$key]->$keyName = $cd->Content;
					}
				}
			}
		}
		return $allContent;		
	}
	public function GetContentByType($typeName,$groups)
	{
		$data = array();
		$groupsPHs = array();
		
		foreach ($groups as $key => $group) {
			$ph = ":group{$group->Id}";
			$data[$ph] = $group->Id;
			$groupsPHs[] = $ph;
		}
		$groupIdsIns = 'IN ('.implode(',',$groupsPHs).')';
		$query = "SELECT * FROM `view_languagecontent` WHERE `TypeName` = :typeName AND `UserGroupId` {$groupIdsIns} GROUP BY `ContentId` ORDER BY `UserId` DESC";
		$prep = $this->db->prepare($query);
		$data[':typeName'] = $typeName;

		$prep->execute($data);
		$allContent = $prep->fetchAll(\PDO::FETCH_OBJ);
		if (!empty($allContent))
		{
			$content = $allContent[0];
			if (!$allContent || empty($allContent)) throw new Exception("Access Denied");
			
			$contentDataModel = new ContentDataModel($this->db);
			
			$groupIds = array();
			
			foreach($allContent as $c) $groupIds[] = $c->UserGroupId;
			
			foreach ( $allContent as $key => $content)
			{
				$contentData = $contentDataModel->GetContentDataByContentId($content->ContentId,$groupIds);

				foreach ($contentData as $cd)
				{
					if ( !array_key_exists($cd->Name,$content) )
					{
						$keyName = $cd->Name;
						$allContent[$key]->$keyName = $cd->Content;
					}
				}
			}
		}
		return $allContent;
	}
	public function Search($parameters)
	{
		
	}
}