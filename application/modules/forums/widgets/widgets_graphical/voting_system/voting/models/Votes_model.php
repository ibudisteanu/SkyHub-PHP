<?php


require_once APPPATH.'modules/forums/widgets/widgets_graphical/voting_system/voting/models/Vote_model.php';

class Votes_model extends MY_Hierarchy_model
{
    public function __construct($bEnableChildren=true)
    {
        parent::__construct($bEnableChildren);
    }

    public function findOrCreateVoteObjectFromParentObjectId($sParentObjectId='')
    {
        $VoteObject = $this->loadContainerByFieldName("ParentObjectId",new MongoId($sParentObjectId));
        if ($VoteObject == null)
        {
            $VoteObject = new Votes_model(true);
            $VoteObject->sParentObjectId = $sParentObjectId;
            $VoteObject->iVoteCountValue=0;
            $VoteObject->storeUpdate();
        }

        return $VoteObject;
    }

    public function findVoteObjectFromParentObjectId($sParentObjectId='')
    {
        return $this->loadContainerByFieldName("ParentObjectId",new MongoId($sParentObjectId));
    }

}