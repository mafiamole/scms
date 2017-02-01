<?php
interface IModel
{
	public function Add($data);
	public function Edit($data);
	public function Delete($id,$chain = false);
	public function Get($id);
	public function GetAll();
	public function Search($parameters);
}
function FileUpload($directoryFolder,$targetKey,$expectedType,$sizeLimit = 500000)
{
	$key = str_replace(" ","_",$targetKey);
	if ( !array_key_exists($key,$_FILES) )
	{
		return false;
	}
	$file = $_FILES[$key];
	$fileURL = $directoryFolder . basename($file["name"]);
	if (strlen($file["tmp_name"]) == 0)
		return;
	$targetFile = getcwd(). $fileURL;
	$extension = pathinfo($targetFile,PATHINFO_EXTENSION);
	$directory = pathinfo($targetFile,PATHINFO_DIRNAME);
	if (strtoupper($expectedType) == "IMAGE") {
		
		// Check if image file is a actual image or fake image
		$validExtension = in_array(strtolower($extension),array('jpg','jpeg','png','gif','svg'));
		$check = getimagesize($file["tmp_name"]);
		if (!$check || !$validExtension) {
			throw new Exception("Invalid image file");
		}
	}
	if ( file_exists($targetFile) )
	{
		throw new Exception("File exists");
	}
	if ($file['size'] > $sizeLimit) throw new Exception("File size exceeded");
	if (!is_dir($directory)) {
		mkdir($directory,0755,true);
	}
	$movedFile = move_uploaded_file($file['tmp_name'],$targetFile);
	if (!$movedFile)
		throw new Exception("Unable to upload your file");

	return $fileURL;
}

class MySQLModelHelper {
	protected $db;
	protected $table;
	protected $fields;
	public function __construct($db,$table,$fields)
	{
		$this->db		= $db;
		$this->table 	= $table;
		$this->fields 	= $fields;
	}
	protected function select($data,$where)
	{
		$query = "SELECT * FROM `{$this->table}`" . ($where?" WHERE {$where}":"");
		$prepQry 	= $this->db->prepare($query);
		$prepQry->execute($data);
		return $prepQry;
	}
	// Basic select query
	public function Get($data,$where)
	{
		$prepQry = $this->select($data,$where);
		return $prepQry->fetch(PDO::FETCH_OBJ);
	}
	// Basic select query
	public function GetAll($data = array(),$where = null)
	{
		$prepQry = $this->select($data,$where);
		return $prepQry->fetchAll(PDO::FETCH_OBJ);		
	}
	public function Add($data,$requiredFields)
	{
		$this->checkRequired($data,$requiredFields);
		$includedFields = array();
		$placeHolders = array();
		$preparedData = array();
		foreach ($this->fields as $field)
		{
			if ( array_key_exists($field,$data) )
			{
				$value = $data[$field];
				$placeHolder = ":{$field}";
				$includedFields[] = $field;
				$placeHolders[] = $placeHolder;
				$preparedData[$placeHolder] = $value;
			}	
		}
		$insertFields = "`".implode('`,`',$includedFields)."`";
		$placeHoldersStr = implode(',',$placeHolders);
		$query = "
		INSERT INTO {$this->table} ($insertFields)
		VALUES ($placeHoldersStr)";
		$prepQry 	= $this->db->prepare($query);
		$done = $prepQry->execute($preparedData);
	
		if (!$done) {
			Debug($query);
			Debug($data);
			Debug($prepQry->errorInfo());
		}
		return $this->db->lastInsertId();			
	}
	public function Edit($data,$where)
	{
		$data = $this->filterExisting($data);
		$preparedData = array();
		$preparedFields = array();
		foreach ($data as $key => $value)
		{
			$placeHolder = ":{$key}";
			$preparedData[$placeHolder] = $value;
			$preparedFields[] = "`{$key}` = {$placeHolder}";
		}
		$set = implode(",",$preparedFields);
		$query = "UPDATE {$this->table} SET {$set} WHERE {$where};";
		$prepQry 	= $this->db->prepare($query);
		$success = $prepQry->execute($preparedData);
		if (!$success) {
			Debug($query);
			Debug($data);
			Debug($prepQry->errorInfo());
		}
		return $success;
	}
	public function Delete($value,$field = "Id") {
		$query = "DELETE FROM {$this->table} WHERE {$field} = ?";
		$prepQry 	= $this->db->prepare($query);
		return $prepQry->execute(array($value));		
	}
	protected function filterExisting($data)
	{
		$returnData = array();
		foreach ( $data as $field => $value )
		{
			if ( in_array($field,$this->fields) ) {
				$returnData[$field] = $value;
			}
		}
		return $returnData;
	}
	protected function checkRequired($data,$requiredFields)
	{
		foreach ( $requiredFields as $requiredField)
		{
			if ( !array_key_exists($requiredField,$data) )
				throw new Exception("Required field {$this->table}::{$requiredField} is ommitted.");
		}
	}
}
