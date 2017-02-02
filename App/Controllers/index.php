<?php

$this->AddData('page_title','Home');
$this->Add
(
	REQUEST_GET,
	'',
	function($controller,$route,$parameters,$models)
	{
		$view = $this->CreateView();
		echo $view->Show('index');
	}
);

