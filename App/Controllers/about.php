<?php

$config['page_title'] = "About " . $config['site_title'];
$data['about_site'] = "A nice blurb of the site to go here";
$contentView = new View($theme,$defaults,$data,$config);
$data['page_content'] = $contentView->show('about.tpl.php');