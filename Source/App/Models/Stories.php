<?php

namespace Models;

class Stories extends SimpleContent
{
    public function __Construct($db,$name="Story")
    {
        parent::__Construct($db,$name);
    }

    public function Get($id)
    {
        $story = $this->cModel->Get($id);        
    }

    public function GetAll()
    {
        $groups     = \Users::GetGroups();
        $cats = $this->cModel->GetContentByType("StoryCategory",$groups);
        foreach ($cats as $catKey => $cat)
        {
            $catStories             = $this->cModel->GetChildItems($cat->ContentId,$groups);
            $cats[$catKey]->Stories = $catStories;
        }
        return $cats;
    }

    public function GetPosts($questId)
    {
        $groups = \Users::GetGroups();
        $content->Posts     =  $contentModel->GetChildItems($questId,$groups);
        $ranks              = new Ranks();
        $ranks              = $ranks->GetAll();
        $characters         = new Characters();
        $characters         = $characters->GetAll();
        foreach ($content->Posts as $key => $post)
        {

            if (isset($post->Characters))
            {
                $characterIds = explode(",",$post->Characters);
                $content->Posts[$key]->Characters = array();
                foreach($characterIds as $char)
                {
                    $foundChar = FindContent($char,$characters);
                    if ( $foundChar )
                    {
                        $foundChar->Rank = FindContent($foundChar->Rank,$ranks);
                        $content->Posts[$key]->Characters[] = $foundChar;
                    }
                }
            }
            else
            {
                $post->Characters = array();
            }

        }        
    }
    public function GetCategories()
    {
        $groups     = \Users::GetGroups();
        $questCats  = $this->cModel->GetContentByType("StoryCategory",$groups);
        foreach ($questCats as $key => $questCat)
        {
            $questCats[$key]->Quests = $contentModel->GetChildItems($questCat->ContentId,$groups);
            foreach ($questCats[$key]->Quests as $QKey => $quest)
            {
                $questCats[$key]->Quests[$QKey]->Posts = $contentModel->GetChildItems($quest->ContentId,$groups); // TODO: Change to a count!
            }
	   }
        return $questCats;
    }
        
    public function Post($post,$questId)
    {
        $quest = $this->Get($questId);
        $pType = $this->GetType("StoryPost");
        if ($quest)
        {
            $post['Parent_Od']          = $questId * 1;
            $post['URL']		= "/quests/view/";

            $post['Users_Id']           = $_SESSION['user']->Id;
            $post['ContentTypes_Id'] 	= $contentType->Id;
            $post['Applications_AppId'] = 1;
            $post['Languages_Id']	= $_SESSION['user']->Languages_id;
            $post['Keywords'] 		= "";

            $characters 		= array();
            foreach($post['UsersCharacters'] as $char)
            {
                $characters[] = $char * 1;
            }
            /*
            foreach($post['OthersCharacters'] as $char)
            {
                $characters[] = $char * 1;
            }*/		
            $post['Characters']		= implode(",",$characters);
            $contentId                  = $this->cModel->Add($post);
            return true;       
        }
        return false;
    }
}