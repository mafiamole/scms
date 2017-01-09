<?php
namespace /models;

public ContentLangModel implements Model
{
	protected $db;
	protected $dbHelper;
	
	public function __construct($db)
	{
		$this->db 		= $db;
		$this->dbHelper = new MySQLModelHelper($db,"ContentLang",array(
			'Id','Content_id','Languages_id','Title','Keywords','Description','Created','LastModified'
		));
	}
	public function Add($data)
	{
		if (!isset($data['Created']) )
		{
			$data['Created'] = date('c');
		}		
		$this->dbHelper->Add($data,array('Content_id','Languages_id','Title','Keywords','Description','Created'));
	}
	public function Edit($data)
	{
		if ( !isset($data['LastModified']) )
		{
			$data['LastModified'] = date('c');
		}
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
	public function Search($parameters)
	{
		
	}
}