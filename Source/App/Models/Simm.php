<?php

namespace Models;

class Simm extends SimpleContent {

    public function __Construct($db, $name = "PositionGroup") {
        parent::__Construct($db, $name);
    }

    /**
     * Fetches a simms manifest from the content table
     * @return type
     */
    public function Manifest($languageId = null) {
        $groups = \Users::GetGroups();
        $positionGroupsData = $this->cModel->GetContentByType($this->name, $groups);
        foreach ($positionGroupsData as $key => $pg) {
            foreach ($pg->data as $langId => $item) {
                if ($languageId) {
                    if ($item->Languages_Id == $languageId) {
                        $positionGroupsData[$key]->Positions = $this->GetPositions($pgcl, $languageId, $groups);
                    }
                } else {
                    $positionGroupsData[$key]->Data[$item->Languages_Id]->Positions = $this->GetPositions($pgcl, $languageId, $groups);
                }
            }
        }
        return $positionGroupsData;
    }

    /**
     * Get all positions
     * @param \Models\Content $positionGroup
     * @param array $groups
     * @return array
     */
    protected function GetPositions($positionGroup, $languageId = null, $groups) {
        $contentLangId = $positionGroup->Id;
        $positions = $this->cModel->GetChildItems($contentLangId, $groups);

        foreach ($positions as $key2 => $pos) {
            foreach ($pos->Data as $langId => $pcl) {
                if ($languageId) {
                    if ($languageId == $pcl->Languages_Id) {
                        if (property_exists($pcl, 'Character') && ($pcl->Character * 1) > 0) {
                            $positions[$key2]->Data->Character = $this->GetCharacter($pcl->Character * 1);
                        } else {
                            $positions[$key2]->Data->CanApply = \Users::LoggedIn();
                        }
                    }
                } else {
                    $langId = $pcl->Languages_Id;
                    if (property_exists($pcl, 'Character') && ($pcl->Character * 1) > 0) {
                        $positions[$key2]->Data[$langId]->Character = $this->GetCharacter($pcl->Character * 1);
                    } else {
                        $positions[$key2]->Data[$langId]->CanApply = \Users::LoggedIn();
                    }
                }
            }
        }

        return $positions;
    }

    /**
     * 
     * @param type $id
     * @return \Models\stdclass
     */
    protected function GetCharacter($id) {
        $charModel = new \Models\Characters(\Common::LocalDB());
        $c = $charModel->Get($id * 1);
        if ($c) {
            $canEdit = (\Users::LoggedIn() && $_SESSION['user']->Id == $c->AuthorId);
            $c->CanEdit = $canEdit;
        } else {
            $c = new stdclass();
        }
        return $c;
    }

    public function Description() {
        return array
            (
            'Title' => "Simm Description",
            "Body" => "Bacon ipsum dolor amet alcatra porchetta hamburger capicola shoulder. Jerky turducken bresaola corned beef pancetta pig, turkey pastrami. Jowl ham hock tenderloin shoulder leberkas tongue turducken tri-tip, corned beef cow spare ribs. Shankle pork capicola, doner fatback alcatra pig beef ham hock cow chicken landjaeger. Fatback bresaola drumstick chicken."
        );
    }

}
