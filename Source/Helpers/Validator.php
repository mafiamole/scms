<?php

class Validator
{
	public static function CheckString($variable)
	{
		return (($variable != null) && strlen($variable) > 0);
	}
	public static function CheckInteger($variable)
	{
		return (($variable != null) && filter_var($variable, FILTER_VALIDATE_INT) !== false);
	}
	public static function CheckEmail($variable)
	{
		if ( $variable == null) return false;
		if ( strlen($variable) == 0 ) {
			return false;
		}
		$match = preg_match("/^\S+@\S+$/",$variable);// TODO: upgrade this check
		
		//$match = !(filter_var($variable,FILTER_VALIDATE_EMAIL) === false);
		return $match === 1;
	}
	public static function CheckPassword($variable)
	{
		return ($variable) && (strlen($variable) > 0);// TODO: upgrade this check
	}
}

class Sanitize
{
	protected $arr;
	protected $errs;
	public function __construct(array $arr) {
		$this->arr = $arr;		
		$this->errs = array();
	}
	public function GetErrors()
	{
		return $this->errs;
	}
	protected function AddError($key,$message)
	{
		if ( array_key_exists($key,$this->errs) )
		{
			$this->errs[$key] += " {$message}";
		} else {
			$this->errs[$key] = $message;
		}
	}
	public function CheckString($key) {
		if(!array_key_exists($key,$this->arr))
		{
			$this->errs[$key] = "Please enter a value";
			return false;
		}
		if (Validator::CheckString($this->arr[$key]))
		{
			$this->errs[$key] = "Invalid value";
			return false;
		}
		return Sanitize::CheckArrString($this->arr[$key]);
	}
	public function CheckInteger($key) {
		if(array_key_exists($key,$this->arr))
		{
			$this->errs[$key] = "Please enter a value";
			return false;
		}
		if(Validator::CheckInteger($this->arr[$key]))
		{
			$this->errs[$key] = "Invalid value";
			return false;
		}
		return Sanitize::CheckArrInteger($this->arr[$key]);		
	}
	public function CheckEmail($key) {
		if(!array_key_exists($key,$this->arr))
		{
			$this->errs[$key] = "Email not found";
			return false;
		}
		$valid = Sanitize::CheckEmailVar($this->arr[$key]);			
		if ( !$valid ) {
			$this->errs[$key] = "Invalid email entered";
		}
		return $valid;
	}
		
	public function CheckPassword($key) {
		if (!array_key_exists($key,$this->arr)) {
			$this->errs[$key] = "Please enter $key";
			return false;
		}
		if(!Validator::CheckPassword($this->arr[$key])) {
			$this->errs[$key] = "Invalid password";
			return false;
		}
		$valid =  Sanitize::CheckPasswordVar($this->arr[$key]);
		if ( !$valid ) {
			$this->errs[$key] = "Invalid password";
		}		
		return $valid;
	}
	public function CheckRegPassword($key,$cKey) {
		if(!(array_key_exists($key,$this->arr))) {
			$this->errs[$key] = "Please enter a password";
			return false;
		}
		if(!(array_key_exists($cKey,$this->arr))) {
			$this->errs[$cKey] = "Please confirm your password";
			return false;
		}
		if ( strlen(Validator::CheckPassword($this->arr[$key]) ) == 0)
		{
			$this->errs[$key] = "Please enter a password";
		}
		
		if ( $this->arr[$key] != $this->arr[$cKey] ) {
			$this->errs[$cKey] = "Passwords do not match";
			return false;
		}
		$valid =  Sanitize::CheckPasswordVar($this->arr[$key]);
		if ( !$valid ) {
			$this->errs[$key] = "Invalid password";
		}
		return $valid;
	}
	public static function CheckStringVar($variable)
	{
		if (!Validator::CheckString($variable))
			return false;
		
		return strip_tags($variable);
	}
	public static function CheckIntegerVar($variable)
	{
		if (!Validator::CheckInteger($variable))
			return false;
		
		return $variable * 1;
	}
	public static function CheckEmailVar($variable)
	{
		$variable = filter_var($variable,FILTER_SANITIZE_EMAIL);

		if ( !Validator::CheckEmail($variable) )
			return false;

		return $variable;
	}
	public static function CheckPasswordVar($password)
	{
		if ( ($password != null) && strlen($password) == 0 )
			return false;
		
		return password_hash($password, PASSWORD_BCRYPT);
	}
}