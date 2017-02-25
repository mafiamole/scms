<?php
namespace Models;

class Characters extends \Models\SimpleContent
{
    public function __Construct($db,$name="Character")
    {
    parent::__Construct($db,$name);
    }
    public function Get($id)
    {        
        $character = parent::Get($id);        
        $character = $this->GetContentData($character);
        return $character;
    }
    public function Add($data)
    {                
        if (!(isset($_SESSION['user']) && isset($_SESSION['user']->Id)))
        {
            return false;
        }
        $groups                         = \Users::GetGroups();
        // We need to filter the darta.
        $characterType                  = $this->GetType();

        $data['ContentTypes_id']        = $characterType->Id;
        $data['Users_id']               = $_SESSION['user']->Id * 1;
        $data['Applications_AppId']     = 1;
        $contentId                      = $this->cModel->Add($data);
        if ($contentId * 1 == 0)
        {
            Debug("Well shite, we have no proper content :(");
            return false;
        }
        $usersLanguage           = $_SESSION['user']->Languages_id * 1;
        foreach($data as $key => $value)
        {
            $data[str_replace("_"," ",$key)] = $value;
        }
        $positionData                   = array();
        $positionData['Status']         = "Closed";
        $positionData['Character']      = $contentId;
        $positionData['Languages_Id']   = $_SESSION['user']->Languages_id * 1;
        $success = $this->contentDataModel->EditAllContentData($positionData,$data['Position']*1,$usersLanguage,$groups);
        return $contentId;
    }
    public function Edit($data)
    {
        if (!$_SESSION['user']->Id)
        {
            return false;
        }
        $groups                         = \Users::GetGroups();
        $usersLanguage                  = $_SESSION['user']->Languages_id * 1;
        $characterType                  = $this->GetType();
        $PosTypeModel 			= $this->ctm->Get("Position");
        $data['ContentId']              = $data['ContentId']*1;
        $data['ContentTypes_id']        = $characterType->Id;
        $data['Users_id'] 		= $_SESSION['user']->Id;
        $data['Applications_AppId']     = 1;
        $data['Id']                      = $data['ContentId'];
        $contentId                      = $this->cModel->Edit($data);
        foreach($data as $key => $value)
        {
            $data[str_replace("_"," ",$key)] = $value;
        }
        try {
            $positionData                   = array();            
            $positionData['Status']         = "Closed";
            $positionData['Character']      = $contentId;
            $success                        = $this->contentDataModel->EditAllContentData($positionData,$data['Position']*1,$usersLanguage,$groups);
        } catch(Exception $exception) {

        }
        return $contentId;
    }
}