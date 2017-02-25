<?php

$this->AddModel("SimmsModels", "Characters");
$this->AddModel("SimmsModels", "Stories");
$this->AddModel("SimmsModels", "Ranks");

$this->Add
(
    REQUEST_POST, '/create', function($controller, $route, $parameters, $models)
    {
        $this->ShowView('createStory.tpl.php');
    }
);
$this->Add
(
REQUEST_POST, '/post/([0-9]*)', function($controller, $route, $parameters, $models)
{
    $id = $parameters[1] * 1;
    $this->data->Add('PostData', array(
        'Title' => "",
        'Description' => "",
        'UsersCharacters' => array(),
        'OthersCharacters' => array()
    ));
    
    $postErr = array();
    if (isset($_POST) && !empty($_POST)) {
        if (isset($_POST['Title'])) {
            $this->data['PostData']['Title'] = strip_tags($_POST['Title']);
        }

        if (isset($_POST['Description'])) {
            $body = strip_tags($_POST['Description']);
            if (strlen($body) <= 0) {
                $err['Description'] = 'Please enter the body of your post';
            }
            $this->data['PostData']['Description'] = $body;
        } else {
            $postErr['Description'] = 'Description not found';
        }
        if (isset($_POST['UsersCharacters']) && count($_POST['UsersCharacters']) > 0) {
            foreach ($_POST['UsersCharacters'] as $character) {
                $this->data['PostData']['UsersCharacters'][] = $character * 1;
            }
        } else {
            $postErr['UsersCharacters'] = 'No user characters selected';
        }
        if (isset($_POST['OthersCharacters']) && count($_POST['OthersCharacters']) > 0) {
            foreach ($_POST['OthersCharacters'] as $character) {
                $this->data['PostData']['OthersCharacters'][] = $character * 1;
            }
        }
        $success = $stories->Add($this->Data['PostData'], $id);
        if ($success) {
            header("locations:/stories/view/" . $id);
        }
    }
    $this->errors->Add('Post', $postErr);
}
);
$this->Add
(
    REQUEST_POST | REQUEST_GET, '/post', function($controller, $route, $parameters, $models)
    {
        $this->data('page_title', "Create a new Quest Post");
        $characters = $this->models->Characters->GetAll();
        $this->data->Add('UsersCharacters', $characters);
        $this->data->Add('OthersCharacters', $characters);
        $this->ShowView('createStoryPost');
    }
);
$this->Add
(
    REQUEST_POST | REQUEST_GET, '/create',
    function($controller, $route, $parameters, $models)
    {
        $this->data('page_title', "Create a new Quest");
        $this->ShowView('createStory');
    }
);
$this->Add
(
    REQUEST_GET, '/view/([0-9]*)',
    function($controller, $route, $parameters, $models)
     {
        $id = $parameters[1] * 1;
        $story = $this->models->Stories->GetCategories($id);
        $this->data->Add('Story', $story);
        if ($story != null) {
            $this->config->Add('page_title', "Viewing " . $quest->ContentTitle);
        }
        $this->ShowView('story');
    }
);
$this->Add
(
    REQUEST_GET, '/?$',
    function($controller, $route, $parameters, $models) {
        $stories = $this->models['Stories']->GetAll();
        $this->config->Add('page_title', "Stories");
        $this->data->Add('StoryCategories', $stories);
        $this->ShowView('stories');
    }
);
