<?php
namespace /models;

public ContentTypeFieldsModel implements Model
{
	protected $db;
	protected $dbHelper;
	
	public function __construct($db)
	{
		$this->db 		= $db;
		$this->dbHelper = new MySQLModelHelper($db,"ContentTypeFields",array(
			'Id','ContentType_id','Name','Type'
		));
	}
	public function Add($data)
	{
		$this->dbHelper->Add($data,array('ContentType_id','Name','Type'));
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
	public function GetContentTypesFields($contentType)
	{
		return $this->dbHelper->GetAll(array('contentType'=>$contentType),"`ContentTypes_id`=:contentType");	
	}
	public function GetAll()
	{
		return $this->dbHelper->GetAll();	
	}
	public function Search($parameters)
	{
		
	}
}