<?php
namespace /models;
public ContentDataAccessRightsModel implements Model
{
	protected $db;
	protected $dbHelper;
	
	public function __construct($db)
	{
		$this->db 		= $db;
		$this->dbHelper = new MySQLModelHelper($db,"ContentDataAccessRights",array(
			'Id','ContentTypesFields_id','UserGroups_id','Value'
		));
	}
	public function Add($data)
	{
		$this->dbHelper->Add($data,array('ContentTypesFields_id','UserGroups_id','Value'));
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
	public function GetContentDataAccess($contentDataTypeId)
	{
		return $this->dbHelper->Get(array('id'=>$contentDataTypeId),"`ContentTypeFields_id` = :id");	
	}
	public function GetGroupAccess($group)
	{
		return $this->dbHelper->Get(array('id'=>$group),"`UserGroups_id` = :id");
	}
	public function GetGroupContentDataAccess($group,$content)
	{
		return $this->dbHelper->Get(array('content'=>$content,'group'=>$group),"`ContentTypeFields_id` = :content AND `UserGroups_id` = :group");
	}
	public function GetLoggedOutContentDataAccess($content)
	{
		$query = "SELECT * FROM `loggedoutcontentdataaccessrights` WHERE `ContentTypesFields_id` = :content";
		$prepQry 	= $this->db->prepare($query);
		$prepQry->execute();
		return $prepQry->fetch();
	}
	public function Search($parameters)
	{
		
	}
}

public ContentAccessRightsModel implements Model
{
	protected $db;
	protected $dbHelper;
	
	public function __construct($db)
	{
		$this->db 		= $db;
		$this->dbHelper = new MySQLModelHelper($db,"ContentAccessRights",array(
			'Id','ContentTypes_id','UserGroups_id','Value'
		));
	}
	public function Add($data)
	{
		$this->dbHelper->Add($data,array('ContentTypes_id','UserGroups_id','Value'));
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
	public function GetContentAccess($contentDataId)
	{
		return $this->dbHelper->Get(array('id'=>$contentDataId),"`ContentTypes_id` = :id");	
	}
	public function GetGroupAccess($group)
	{
		return $this->dbHelper->Get(array('id'=>$group),"`UserGroups_id` = :id");
	}
	public function GetGroupContentAccess($group,$content)
	{
		return $this->dbHelper->Get(array('id'=>$contentDataTypeId),"`UserGroups_id` = :id");
	}
	public function GetLoggedOutContentDataAccess($content)
	{
		$query = "SELECT * FROM `loggedoutcontentaccessrights` WHERE `ContentTypes_id` = :content";
		$prepQry 	= $this->db->prepare($query);
		$prepQry->execute();
		return $prepQry->fetch();
	}
	public function Search($parameters)
	{
		
	}
}
