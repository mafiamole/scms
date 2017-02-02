<?php
/**
 * Collection class encapsulating our view data. It has the following features
 * When an instance is used like a method, it will echo/display the relevant data item 
 * When accessing a property that is an object or array directly it will return a ViewData encapsulated version that allows you use the same functionality.
 * When used as an iterable item, it will iterate through the data items, encapsulating the data (if object or array) in an instanceof ViewData
 */
class ViewData implements Iterator
{
	protected $data = array();
	protected $showErrors = false;
	public function __construct(array $data,bool $showErrors = false)
	{
		$this->data = $data;
		$this->showErrors = $showErrors;
	}
	/**
	 *
	 * Displays a value from our data by allowing this object to be invoked like a function
	 * This is to make the view code more 'tidy'
	 *
	 */
	public function __invoke($key)
	{
		try
        {
			$this->Show(func_get_args());
		}
		catch (Exception $exception)
		{
			return ( $this->showErrors?$exception->GetMessage():"" );
		}		
	}
    public function __get($key)
    {
        return $this->Get($key);
    }
    public function Count($key)
    {
        try {
            $key = $this->ProcessArguments($key);
            $value = $this->GetValue($key);
            if ($value && is_array($value))
            {
                return count($value);
            }
            else
            {
                return 0;
            }
		}
		catch (Exception $exception)
		{
			return ( $this->showErrors? $exception->GetMessage():"" );
		}         
    }

    public function Equals($key,$toMatch,$true=true,$false=false)
    {
        try
        {
            $key = $this->ProcessArguments($key);
            $value = $this->GetValue(array($key));
            return ($value == $toMatch)?$true:$false;
		}
		catch (Exception $exception)
		{
			return ( $this->showErrors? $exception->GetMessage():"" );
		}            
    }

	public function GetAll($key)
	{
        try{
            $args = func_get_args();
            $value = "";
            $args = $this->ProcessArguments($args);
            $value = $this->GetValue($args);

            if (is_array($value) || is_object($value)) return $this->WrapArray((array)$value);

            else return array($value);

            return $value;
		}
		catch (Exception $exception)
		{
			return ( $this->showErrors? $exception->GetMessage():"" );
		}
	}

	public function Get($key)
	{		
        try{
            $args = func_get_args();
            $value = "";
            $args = $this->ProcessArguments($args);
            $value = $this->GetValue($args);
            return $this->WrapReturnValue($value);
		}
		catch (Exception $exception)
		{
			return ( $this->showErrors? $exception->GetMessage():"" );
		}            
	}
	public function Show($key)
	{
        try{
            echo $this->Get($key);
		}
		catch (Exception $exception)
		{
			return ( $this->showErrors? $exception->GetMessage():"" );
		}            
	}
	public function Add($key,$value)
	{
        try{
		  $this->data[$key] = $value;
		}
		catch (Exception $exception)
		{
			return ( $this->showErrors? $exception->GetMessage():"" );
		}            
	}
	public function Has($key)
	{
        try{
			return array_key_exists($key,$this->data);
           // return $this->arrHas(array($key),$this->data);
		}
		catch (Exception $exception)
		{
			return ( $this->showErrors? $exception->GetMessage():"" );
		}            
	}
    // { Iterator methods
    public function rewind()
    {
        reset($this->data);
    }
  
    public function current()
    {
        $var = current($this->data);
        return $this->WrapReturnValue($var);
    }
  
    public function key() 
    {
        $var = key($this->data);
        return $var;
    }
  
    public function next() 
    {
        $var = next($this->data);
        return $var;
    }
  
    public function valid()
    {
        $key = key($this->data);
        $var = ($key !== NULL && $key !== FALSE);
        return $var;
    }
     // }
	protected function GetValue($keys)
	{
		$value = "";
		$value = $this->fetch($keys,$this->data);        
		return  $value;
	}
	// if the return value is not scalar, it will create  a new ViewData object with it. 
	protected function WrapReturnValue($retValue)
	{
		
		if (is_scalar($retValue) || $retValue instanceof ViewData) return $retValue;
		else return new ViewData((array)$retValue,$this->showErrors);
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
    protected function ProcessArguments($args)
    {
        if (!is_array($args)) return $args;
        
        if (count($args) > 1) return $args;

        if (is_array($args[0])) return $args[0];
        
        return $args;
    }

    protected function arrHas($keys,$arr)
    {	
        $args = $this->ProcessArguments(func_get_args());
        if (count($args) == 1) return array_key_exists($args[0],$arr);
		return $this->fetchIterative($keys,$args) !== false;
    }

    protected function fetch($keys,$arr)
    {
        if(count($keys) == 1)
        {
            $arr = $this->extractValue($keys[0],$arr);
            if ( $arr === false ) return ($this->showErrors?"Key $key does not exist":"");
            else return $arr;        
        }
        else if (count($keys) == 0)
        {
            return ($this->showErrors?"Please enter a parameter":"");
        }
        else
        {
            $value = $this->fetchIterative($keys,$arr);
            if (!$value) return ($this->showErrors?"Key $key does not exist":"");
            return $value;
        }
        return false;
    }
	
    protected function fetchIterative($keys,$arr)
    {
        foreach($keys as $key)
        {
            $arr = $this->extractValue($key,$arr);
            if (!$arr) return false;
        }
        return $arr;
    }
    protected function extractValue($key,$arr)
    {
         if (is_array($arr) && array_key_exists($key,$arr))
        {
            $arr = $arr[$key];
        }
        else if ( is_object($arr) && property_exists($arr,$key))
        {
            $arr = $arr->$key;
        }
        else
        {
            //$key = strip_tags($key);
            //throw new Exception("Key {$key} does not exist");
            return false;
        }
        return $arr;
    }
}

class View {
	
	protected $theme;
	protected $data;
	protected $config;
    protected $errors;
	
	public function __construct($theme,$data,$config) {
		$this->theme	= $theme;
        $this->data     = $this->initaliseDataObject($data);
        $this->config   = $this->initaliseDataObject($config);
        $this->errors  	= $this->initaliseDataObject(array());
    }

	public function Show($template) {
		ob_start();
		$parameters = $this->data->Get('parameters');
		$data       = $this->data;
		$config     = $this->config;
        $errors     = $this->errors;
		require(APP_FOLDER . "/Themes/{$this->theme}/". $template.'.tpl.php');
		return ob_get_clean();
	}
	/**
	 * Run a controller, aimed for smaller shared modules to be included on a template.  
	 *
	 *  @Param string $controllerName The name of the controller
	 */
	public function Call($controllerName)
	{
		$controller = new Controller(URL::GetRoot(),$this->data,$this->config);		
		$controller->Initiate($controllerName);
		echo $controller->Run($_SERVER['REQUEST_URI'], CheckRequestMethod());		
	}
	public function AddData($key,$value)
	{
		$this->data->Add($key,$value);
	}
    public function AddError($key,$value)
    {
        $this->errors->Add($key,$value);
    }
	protected function AddConfig($key,$value)
	{
		$this->config->Add($key,$value);
	}
	public function CreateSubView()
	{
		return new View($this->theme,$this->data,$this->config);
	}
	public function ShowView($template)
	{
		$view = $this->CreateSubView();
		$data = $this->data;
		echo $view->Show($template);		
	}
	protected function initaliseDataObject($data)
    {
 		if ( is_array($data) )
		{
			return new ViewData($data);
		}
		else if ( $data instanceof ViewData )
		{
			return $data;
		}
		else
		{
			throw new Exception('Invalid Data structure');
		}       
    }    
}
// Specialised view for the whole page :D
class PageView extends View
{
	public function __construct($theme,$data,$config) {
		parent::__construct($theme,$data,$config);
		
	}
	protected function FormatTitle() {
		$format 	= $this->config->Get('html_title_format');
		$siteTitle 	= $this->data->Get('site_title');
		$pageTitle 	= $this->data->Get('page_title');
		//echo " $siteTitle $pageTitle $format ";
		return sprintf($format,$siteTitle,$pageTitle);
	}
}