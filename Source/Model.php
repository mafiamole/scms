<?php

interface IModel {

    /**
     * Add a record
     * @param array $data
     * @param array $options
     */
    public function Add($data, $options = array());

    /**
     * Edit a record
     * @param array $data
     * @param array $options
     */
    public function Edit($data, $options = array());

    /**
     * Remove a record
     * @param int $id
     * @param bool $chain
     * @param array $options
     */
    public function Delete($id, $chain = false, $options = array());

    /**
     * Get a record by id
     * @param int $id
     * @param array $options
     */
    public function Get($id, $options = array());

    /**
     * Get all items
     * @param array $options
     */
    public function GetAll($options = array());
}

/**
 * 
 * @param \PDO $db
 * @param string $type
 * @param array $record
 * @return \IModel
 */
function PrepareRecord($db, $type, $record) {
    $obj = new $type($db);
    foreach ($record as $fieldName => $fieldValue) {
        $obj->$fieldName = $fieldValue;
    }
    return $obj;
}

/**
 * Returns an array of publically accessible fields for an object
 * @param object $object
 * @return array
 */
function GetPublicFields($object,$ignore= array()) {
    $fields = get_object_vars($object);
    return array_diff($fields, $ignore);
}

/**
 * Gets a field value if it exisits or return the passed in default
 * @param array|object $arr
 * @param key $key
 * @param mixed $default
 * @return mixed
 */
function GetFieldOrDefault($arr, $key, $default = "") {
    if (is_array($arr)) {
        return array_key_exists($key, $arr) ? $arr[$key] : $default;
    } else {
        return property_exists($arr, $key) ? $arr->$key : $default;
    }
}

/**
 * Performs a file upload
 * 
 * For images, the following files are supported: jpg,jpeg,png,gif,svg
 * 
 * @param string $directoryFolder
 * @param string $targetKey key in _FILES array 
 * @param string $expectedType Used to validate the file, set to IMAGE to check if it is an image file 
 * @param type $sizeLimit
 * @return boolean|string
 * @throws Exception
 */
function FileUpload($directoryFolder, $targetKey, $expectedType, $sizeLimit = 500000) {
    $key = str_replace(" ", "_", $targetKey);
    if (!array_key_exists($key, $_FILES)) {
        return false;
    }
    $file = $_FILES[$key];
    $fileURL = $directoryFolder . basename($file["name"]);
    if (strlen($file["tmp_name"]) == 0)
        return;
    $targetFile = getcwd() . $fileURL;
    $extension = pathinfo($targetFile, PATHINFO_EXTENSION);
    $directory = pathinfo($targetFile, PATHINFO_DIRNAME);
    if (strtoupper($expectedType) == "IMAGE") {

        // Check if image file is a actual image or fake image
        $validExtension = in_array(strtolower($extension), array('jpg', 'jpeg', 'png', 'gif', 'svg'));
        $check = getimagesize($file["tmp_name"]);
        if (!$check || !$validExtension) {
            throw new Exception("Invalid image file");
        }
    }
    if (file_exists($targetFile)) {
        throw new Exception("File exists");
    }
    if ($file['size'] > $sizeLimit)
        throw new Exception("File size exceeded");
    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
    }
    $movedFile = move_uploaded_file($file['tmp_name'], $targetFile);
    if (!$movedFile)
        throw new Exception("Unable to upload your file");

    return $fileURL;
}

/**
 * Set of functions for interacting with an sql database
 */
class MySQLModelHelper {

    /**
     * Database connection instance
     * @var \PDO
     */
    protected $db;

    /**
     * Table name
     * @var string
     */
    protected $table;

    /**
     * List of field names
     * @var array
     */
    protected $fields;

    /**
     * Initialise the helper
     * @param \PDO $db
     * @param string $table
     * @param string $fields
     */
    public function __construct($db, $table, $fields) {
        $this->db = $db;
        $this->table = $table;
        $this->fields = $fields;
    }

    /**
     * Builds an select statement
     * @param array $data
     * @param string $where
     * @return \PDOStatement
     */
    protected function select($data, $where) {
        $query = "SELECT * FROM `{$this->table}`" . ($where ? " WHERE {$where}" : "");
        $prepQry = $this->db->prepare($query);
        $prepQry->execute($data);
        return $prepQry;
    }

    /**
     * Performs a basic get query to return a single result
     * @param array $data
     * @param array $where
     * @return object
     */
    public function Get($data, $where) {
        $prepQry = $this->select($data, $where);
        return $prepQry->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Performs a basic get query and return all matched results
     * @param type $data
     * @param type $where
     * @return array
     */
    public function GetAll($data = array(), $where = null) {
        $prepQry = $this->select($data, $where);
        return $prepQry->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Adds a record
     * @param array $data
     * @param array $requiredFields
     * @return int Returns the last inserted index.
     */
    public function Add($data, $requiredFields) {
        $this->checkRequired($data, $requiredFields);
        $includedFields = array();
        $placeHolders = array();
        $preparedData = array();
        foreach ($this->fields as $field) {
            if (array_key_exists($field, $data)) {
                $value = $data[$field];
                $placeHolder = ":{$field}";
                $includedFields[] = $field;
                $placeHolders[] = $placeHolder;
                $preparedData[$placeHolder] = $value;
            }
        }
        $insertFields = "`" . implode('`,`', $includedFields) . "`";
        $placeHoldersStr = implode(',', $placeHolders);
        $query = "
            INSERT INTO {$this->table} ($insertFields)
            VALUES ($placeHoldersStr)";
        $prepQry = $this->db->prepare($query);
        $done = $prepQry->execute($preparedData);

        if (!$done) {
            Debug($query);
            Debug($data);
            Debug($prepQry->errorInfo());
        }
        return $this->db->lastInsertId();
    }

    /**
     * Edits a record
     * @param array $data
     * @param array $where
     * @return bool
     */
    public function Edit($data, $where) {
        $data = $this->filterExisting($data);
        $preparedData = array();
        $preparedFields = array();
        foreach ($data as $key => $value) {
            $placeHolder = ":{$key}";
            $preparedData[$placeHolder] = $value;
            $preparedFields[] = "`{$key}` = {$placeHolder}";
        }
        $set = implode(",", $preparedFields);
        $query = "UPDATE {$this->table} SET {$set} WHERE {$where};";
        $prepQry = $this->db->prepare($query);
        $success = $prepQry->execute($preparedData);
        if (!$success) {
            Debug($query);
            Debug($data);
            Debug($prepQry->errorInfo());
        }
        return $success;
    }
    /**
     * 
     * @param string|int $value
     * @param string $field
     * @return bool
     */
    public function Delete($value, $field = "Id") {
        $query = "DELETE FROM {$this->table} WHERE {$field} = ?";
        $prepQry = $this->db->prepare($query);
        return $prepQry->execute(array($value));
    }
    /**
     * Prepares an array with placeholders for query
     * @param string $prepend string to start the placeholder
     * @param array $arr
     * @return array
     */    
    function PrepArray($prepend, $arr) {
        $data = array();
        foreach ($arr as $key => $value) {
            $placeHolder = ":{$prepend}_{$key}";
            $data[$placeHolder] = $value;
        }
        return $data;
    }
    /**
     * Filters the list of elements against the list of fields
     * @param array $data
     * @return array
     */
    protected function filterExisting($data) {
        $returnData = array();
        foreach ($data as $field => $value) {
            if (in_array($field, $this->fields)) {
                $returnData[$field] = $value;
            }
        }
        return $returnData;
    }
    /**
     * Checks if all required fields are present
     * @param array $data
     * @param array $requiredFields
     * @throws Exception
     */
    protected function checkRequired($data, $requiredFields) {
        foreach ($requiredFields as $requiredField) {
            if (!array_key_exists($requiredField, $data))
                throw new Exception("Required field {$this->table}::{$requiredField} is ommitted.");
        }
    }

}
/**
 * Loads a model from the models folder
 * @param \PDO $db
 * @param string $name
 * @return \IModel
 */
function LoadModel($db, $name) {
    $className = "Models\\$name";
    return new $className($db);
}
