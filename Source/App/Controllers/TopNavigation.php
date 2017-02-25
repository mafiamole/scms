<?php
$this->Add
(
	REQUEST_GET | REQUEST_POST,
	'/?.*',
	function($controller,$route,$parameters,$models)
	{
		$topNav = array
		(
			array('ContentTitle'=>'Home','URL'=>'/','UserGroups'=>array('Id'=>1,2,3)),
			array('ContentTitle'=>'Simm','URL'=>'/simm','UserGroups'=>array('Id'=>1,2,3)),
			array('ContentTitle'=>'Stories','URL'=>'/stories','UserGroups'=>array('Id'=>1,2,3)),
			array('ContentTitle'=>'Login','URL'=>'/users/login','UserGroups'=>array('Id'=>1)),
			array('ContentTitle'=>'Register','URL'=>'/users/register','UserGroups'=>array('Id'=>1)),
			array('ContentTitle'=>'Logout','URL'=>'/users/logout','UserGroups'=>array('Id'=>2,3))
		);
		
		foreach ($topNav as $key => $tn)
		{	
			$regex = "#^".preg_quote($tn['URL'])."#";
			$match = preg_match($regex,$_SERVER['REQUEST_URI']);
			$data['TopNavigation'][$key]['Active'] = ($match === 1?true:false);
		}
		$this->data->Add('TopNavigation',$topNav);
		$this->ShowView('TopNavigation');	
	}
);
