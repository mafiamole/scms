<?php

class ViewData
{
	protected $data = array();
	protected $showErrors = false;
	protected $defaults = array();
	public function __construct(array $data,array $defaults,bool $showErrors = false)
	{
		$this->data = $data;
		$this->showErrors = $showErrors;
		$this->defaults = $defaults;
	}
	/**
	 *
	 * Displays a value from our data by allowing this object to be invoked like a function
	 * This is to make the view code more 'tidy'
	 *
	 */
	public function __invoke($key)
	{
		try{
			$args = func_get_args();
			$value = $this->Get($args);
			echo $value;
		}
		catch (Exception $exception)
		{
			return ( $this->showErrors? $exception->GetMessage():"" );
		}
		
	}
	public function GetAll($key)
	{
		$args = func_get_args();
		$value = "";
		try {
			if ( count($args) > 1 )
			{		
				$value = $this->GetIterative($args);
			}
			else
			{
				if ( is_array($key) )
				{
					$value = $this->GetIterative($key);
				}
				else 
				{
					$value = $this->GetValue($key);
				}
			}
		}
		catch(Exception $exception)
		{
			return ( $this->showErrors? $exception->GetMessage():"" );
		}
		if (is_array($value) || is_object($value))
		{
			return $this->WrapArray((array)$value);
		}
		else
		{
			return array($value);
		}
		return $value;
	}
	protected function WrapArray($arr)
	{
		$returnData = array();
		foreach ($arr as $d)
		{
			$returnData[] = $this->WrapReturnValue($d);
		}
		return $returnData;		
	}
	public function Get($key)
	{		
		$args = func_get_args();
		$value = "";
		try {
			if ( count($args) > 1 )
			{		
				$value = $this->GetIterative($args);
			}
			else
			{
				if ( is_array($key) )
				{
					$value = $this->GetIterative($key);
				}
				else 
				{
					$value = $this->GetValue($key);
				}
			}
			return $this->WrapReturnValue($value);	
		}
		catch(Exception $exception)
		{
			return ($this->showErrors?"[Key not found]":"");
		}
		return $this->WrapReturnValue($value);
	}
	protected function GetValue($key)
	{
		if ( $this->Has($key) )
		{
			return $this->data[$key];
		}
		else
		{
			if ( $this->HasDefault($key) )
			{
				return $this->defaults[$key];
			}
			else
			{
				return ($this->showErrors?"[Key not found]":"");
			}
		}
		return "";
	}
	protected function GetIterative(array $keys)
	{
		$value = "";
		try
		{
			$value = $this->SearchIterative($keys,$this->data);
		}
		catch (Exception $exception)
		{
			$value = $this->SearchIterative($keys,$this->defaults); // fallback to default
		}
		return  $value;
	}
	// if the return value is not scalar, it will create  a new ViewData object with it. 
	protected function WrapReturnValue($retValue)
	{
		
		if (is_scalar($retValue) || $retValue instanceof ViewData)
		{
			
			return $retValue;
		}
		else
		{
			return new ViewData((array)$retValue,array(),$this->showErrors);
		}		
	}
	protected function SearchIterative(array $arrKeys,$arr)
	{
		$data = $this->data;
		foreach ( $arrKeys as $arg ) 
		{
			if ( is_array($data) && array_key_exists($arg,$data) )
			{
				$data = $data[$arg];
			}
			else if ( is_object($data) && property_exists($data,$arg) )
			{
				$data = $data->$arg;
			}
			else
			{
				$arg = strip_tags($arg);
				throw new Exception("Key {$arg} does not exist");
				return false;
			}	
		}
		return $data;
	}
	
	public function Show($key)
	{
		echo $this->Get($key);
	}
	public function Add($key,$value)
	{
		$this->data[$key] = $value;
	}
	public function Has($key)
	{
		return array_key_exists($key,$this->data);
	}
	public function HasDefault($key)
	{
		return array_key_exists($key,$this->defaults);
	}
}

class View {
	
	protected $theme;
	protected $defaults;
	protected $data;
	protected $config;
	
	public function __construct($theme,$default,$data,$config) {
		$this->theme	= $theme;
		$this->defaults = $default;
		if ( is_array($data) )
		{
			$this->data		= new ViewData($data,$default);
		}
		else if ( $data instanceof ViewData )
		{
			$this->data = $data;
		}		
		else
		{
			throw new Exception('Invalid Data structure');
		}
		if ( is_array($config) )
		{
			$this->config	= new ViewData($config,$default);
		}
		else if ( $config instanceof ViewData )
		{
			$this->config = $config;
		}
		else
		{
			throw new Exception('Invalid Config structure');
		}	
	}
	
	public function Show($template) {
		ob_start();
		//$Call = function($controllerName) { $this->Call($controllerName);	};
		//$Show = function($value) { echo (string)$value; };
		$parameters = $this->data->get('parameters');
		$data = $this->data;
		$config = $this->config;
		require(APP_FOLDER . "/Themes/{$this->theme}/". $template);
		return ob_get_clean();
	}
	public function Call($controllerName)
	{
		//$Call = function($controllerName) { $this->Call($controllerName);	};
		//$Show = function($value) { echo (string)$value; };
		$parameters = $this->data->Get('parameters');
		$data = $this->data;
		$config = $this->config;
		require_once(APP_FOLDER ."Controllers/{$controllerName}.php");
	}
	public function AddData($key,$value)
	{
		$this->data->Add($key,$value);
	}
	protected function GetData($key)
	{
		return $this->data->Get($key);
	}
	protected function AddConfig($key,$value)
	{
		$this->config->Add($key,$value);
	}
	protected function GetConfig($key)
	{
		return $this->config->Get($key);
	}
	public function CreateSubView()
	{
		return new View($this->theme,$this->defaults,$this->data,$this->config);
	}
	public function ShowView($template)
	{
		$view = $this->CreateSubView();
		$data = $this->data;
		echo $view->Show($template);		
	}
}
// Specialised view for the whole page :D
class PageView extends View
{
	public function __construct($theme,$default,$data,$config) {
		parent::__construct($theme,$default,$data,$config);
		
	}
	protected function FormatTitle() {
		$format 	= $this->config->Get('html_title_format');
		$siteTitle 	= $this->data->Get('site_title');
		$pageTitle 	= $this->data->Get('page_title');
		//echo " $siteTitle $pageTitle $format ";
		return sprintf($format,$siteTitle,$pageTitle);
	}
}