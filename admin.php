<?php
$data['TopNavigation'] = array
(
    array('ContentTitle'=>'Home','URL'=>'/admin','UserGroups'=>array('Id'=>1,2,3)),
	array('ContentTitle'=>'Content','URL'=>'/admin/content','UserGroups'=>array('Id'=>1,2,3)),
	array('ContentTitle'=>'Users','URL'=>'/admin/users','UserGroups'=>array('Id'=>1,2,3)),
);
$controllers = array(
	new ControllerMap('/admin',"admin",array()),
);
$data['pageController'] = SearchControllerMaps(
							$controllers,
							$_SERVER['REQUEST_URI'],
							new ControllerMap('/',"content",array())
						);

$page = new PageView($theme,$defaults,$data,$config);

echo $page->show('admin.tpl.php');