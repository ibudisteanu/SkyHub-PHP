<?php

//require_once APPPATH.'modules/widgets/widgets_graphical/toolbox/models/Tool_box_element.php';

abstract class TVoteAnswerDatabaseValue
{
    const notVoted=0;
    const votedUp=1;
    const votedDown=2;
    const votedMarked=3;
    const votedMarkedUp=4;
    const votedMarkedDown=5;

    public static function codeAnswer($iVoteValue, $bStarMarked)
    {
        if (!$bStarMarked)
        {
            if ($iVoteValue==0) return TVoteAnswerDatabaseValue::notVoted;
            else if ($iVoteValue==1) return TVoteAnswerDatabaseValue::votedUp;
            else if ($iVoteValue==-1) return TVoteAnswerDatabaseValue::votedDown;
        } else
        {
            if ($iVoteValue==0) return TVoteAnswerDatabaseValue::votedMarked;
            else if ($iVoteValue==1) return TVoteAnswerDatabaseValue::votedMarkedUp;
            else if ($iVoteValue==-1) return TVoteAnswerDatabaseValue::votedMarkedDown;
        }
    }

    public static function decodeAnswer(&$iVoteValue, &$bStarMarker, $enVoteFromDB)
    {
        switch ($enVoteFromDB)
        {
            case TVoteAnswerDatabaseValue::notVoted: $iVoteValue=0; $bStarMarker=0; break;
            case TVoteAnswerDatabaseValue::votedUp: $iVoteValue=1; $bStarMarker=0; break;
            case TVoteAnswerDatabaseValue::votedDown:$iVoteValue=-1; $bStarMarker=0; break;
            case TVoteAnswerDatabaseValue::votedMarked:$iVoteValue=0; $bStarMarker=+1; break;
            case TVoteAnswerDatabaseValue::votedMarkedUp:$iVoteValue=+1; $bStarMarker=+1; break;
            case TVoteAnswerDatabaseValue::votedMarkedDown:$iVoteValue=-1; $bStarMarker=+1; break;
        }
    }
}

class Vote_answer_model extends MY_Hierarchy_model
{

    public $sClassName = 'Vote_answer_model';
    //public $sAuthorId;
    private $iVoteValueDB;

    public $iVoteValue;//positive up, positive down or none
    public $bStarMarked;

    public $arrChildrenDefinition = array();

    public function __construct($bEnableChildren=false, $hierarchyGrandParent = null, $hierarchyParent=null)
    {
        parent::__construct($bEnableChildren, $hierarchyGrandParent, $hierarchyParent);
        $this->initDB('Voting',TUserRole::notLogged, TUserRole::User, TUserRole::Admin, TUserRole::SuperAdmin);

        $this->iVoteValue=0;
        $this->bStarMarked=false;
    }

    public function rewriteStatus($bUpStatus, $bDownStatus, $bStarMarked)
    {
        $iVoteValue = 0;
        if ($bUpStatus) $iVoteValue=+1;
        else
            if ($bDownStatus) $iVoteValue=-1;
        $this->iVoteValueDB = TVoteAnswerDatabaseValue::codeAnswer($iVoteValue, $bStarMarked);
        TVoteAnswerDatabaseValue::decodeAnswer($this->iVoteValue,$this->bStarMarked,$this->iVoteValueDB);
    }

    protected function readCursor($p, $bEnableChildren=null)
    {
        if ($bEnableChildren == null) $bEnableChildren = $this->bEnableChildren;

        if (isset($p['AuthorId'])) $this->sAuthorId =  (string) $p['AuthorId'];

        if (isset($p['VoteValue']))
        {
            $this->iVoteValueDB =  $p['VoteValue'];
            TVoteAnswerDatabaseValue::decodeAnswer($this->iVoteValue,$this->bStarMarked,$this->iVoteValueDB);
        }

    }

    protected function serializeProperties()
    {
        $arrResult = parent::serializeProperties();

        $this->iVoteValueDB = TVoteAnswerDatabaseValue::codeAnswer($this->iVoteValue,$this->bStarMarked);

        if ($this->iVoteValueDB != 0)
            $arrResult = array_merge($arrResult, array("VoteValue"=>$this->iVoteValueDB));


        if ($this->sAuthorId != '')
            $arrResult = array_merge($arrResult, array("AuthorId"=>new MongoId($this->sAuthorId)));

        return $arrResult;
    }

    public function cloneObject($source)
    {
        parent::cloneObject($source);
        $this->iVoteValueDB = $source->iVoteValueDB;
        $this->iVoteValue = $source->iVoteValue;
        $this->bStarMarked = $source->bStarMarked;
    }

}