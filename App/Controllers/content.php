<?php


function FindContent($url,$contentList)
{	
	foreach($contentList as $content)
	{
		if ( $url == $content['url'] ) 
		{
			return $content;
		}
	}
	return false;
}

$this->AddData('page_title','Home');
$this->Add
(
	REQUEST_GET,
	'',
	function($controller,$route,$parameters,$models)
	{
        $contents = array(

            array(
                'url' 	=> "/about",
                'title' =>"About ". $this->data->Get('site_title'),
                'body' 	=> "Bacon ipsum dolor amet alcatra porchetta hamburger capicola shoulder. Jerky turducken bresaola corned beef pancetta pig, turkey pastrami. Jowl ham hock tenderloin shoulder leberkas tongue turducken tri-tip, corned beef cow spare ribs. Shankle pork capicola, doner fatback alcatra pig beef ham hock cow chicken landjaeger. Fatback bresaola drumstick chicken."
                )

        );        
        $this->data->Add('Content', FindContent($_SERVER['REQUEST_URI'],$contents));
		echo $this->ShowView('content');
	}
);

