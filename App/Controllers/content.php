<?php
$contents = array(

	array(
		'url' 	=> "/about",
		'title' =>"About ". $this->GetData('site_title'),
		'body' 	=> "Bacon ipsum dolor amet alcatra porchetta hamburger capicola shoulder. Jerky turducken bresaola corned beef pancetta pig, turkey pastrami. Jowl ham hock tenderloin shoulder leberkas tongue turducken tri-tip, corned beef cow spare ribs. Shankle pork capicola, doner fatback alcatra pig beef ham hock cow chicken landjaeger. Fatback bresaola drumstick chicken."
		)

);

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

$this->data['Content'] = FindContent($_SERVER['REQUEST_URI'],$contents);

$this->ShowView('content.tpl.php');