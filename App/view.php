<?php
class View {
	
	protected $theme;
	protected $defaults;
	protected $data;
	protected $config;
	
	public function __construct($theme,$default,$data,$config) {
		$this->theme	= $theme;
		$this->defaults = $default;
		$this->data		= $data;
		$this->config	= $config;
	}
	
	public function Show($template) {
		ob_start();
		//$Call = function($controllerName) { $this->Call($controllerName);	};
		//$Show = function($value) { echo (string)$value; };
		$parameters = $this->data['parameters'];
		require(APP_FOLDER . "/Themes/{$this->theme}/". $template);
		return ob_get_clean();
	}
	public function Call($controllerName)
	{
		//$Call = function($controllerName) { $this->Call($controllerName);	};
		//$Show = function($value) { echo (string)$value; }; 
		$parameters = $this->data['parameters'];
		require_once(APP_FOLDER ."Controllers/{$controllerName}.php");
	}
	public function AddData($key,$value)
	{
		$this->data[$key] = $value;
	}
	protected function GetData($key)
	{
		return $this->GetDataFromContainer($this->data,$key);
	}
	protected function GetConfig($key)
	{
		return $this->GetDataFromContainer($this->config,$key);
	}
	protected function GetDataFromContainer($container,$key)
	{
		$default = (array_key_exists($key,$this->defaults)?$this->defaults[$key]:"No value for {$key}");
		return array_key_exists($key,$container)?$container[$key]:$default;
	}
	public function CreateSubView()
	{
		return new View($this->theme,$this->defaults,$this->data,$this->config);
	}
	public function ShowView($template)
	{
		$view = $this->CreateSubView();
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
		$format 	= $this->GetDataFromContainer($this->config,'html_title_format');
		$siteTitle 	= $this->GetDataFromContainer($this->data,'site_title');
		$pageTitle 	= $this->GetDataFromContainer($this->data,'page_title');		
		return sprintf($format,$siteTitle,$pageTitle);
	}
}