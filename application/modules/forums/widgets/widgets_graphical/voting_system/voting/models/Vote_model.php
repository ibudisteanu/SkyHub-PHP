<?php

require_once APPPATH.'modules/forums/widgets/widgets_graphical/voting_system/voting/models/Vote_answer_model.php';

class Vote_model extends MY_Hierarchy_model
{
    public $arrChildrenDefinition = array(array("Name"=>"Votes","Class"=>"Vote_answer_model","Array"=>"arrVotes","EnableChildren"=>true, "CreateVisitorsStatistics"=>false));

    public $sParentObjectId;
    public $sGrandParentObjectId;
    public $sClassName = 'Vote_model';
    public $sDefaultChildrenArrayName = 'arrChildren';

    public $iVoteCountValue;//Number of votes already

    //User Voting solution
    public $sUserToCheckId;
    public $iUserVoteStatus;
    public $bUserVoteStarMarked;

    public $bStoreParentObjectId;

    public $moveToPreviousParent = null; //used to send response back to the ajax where the JQUERY should move the current element
    public $moveToPreviousParentAction  = 0; //used to send response back to the ajax where the JQUERY should move the current element

    public function __construct($sUserToCheckId=null, $bStoreParentObjectId=true, $bEnableChildren=false, $sDefaultChildrenArrayName='arrChildren')
    {
        if ($sUserToCheckId == null)  $sUserToCheckId = $this->MyUser->sID;
        $this->sUserToCheckId = $sUserToCheckId;
        $this->bStoreParentObjectId = $bStoreParentObjectId;

        parent::__construct($bEnableChildren);
        $this->sDefaultChildrenArrayName = $sDefaultChildrenArrayName;
        $this->initDB('Voting',TUserRole::notLogged, TUserRole::User, TUserRole::User, TUserRole::SuperAdmin);

        if (!isset($this->iVoteCountValue)) $this->iVoteCountValue=0;
        if (!isset($this->bUserVoteStarMarked)) $this->bUserVoteStarMarked=false;
        if (!isset($this->iUserVoteStatus)) $this->iUserVoteStatus=0;

        $this->getUserAnswers();
    }

    protected function getUserAnswers()
    {
        //echo 'user'.$this->sUserToCheckId;
        if ($this->sUserToCheckId != null) // if logged, check the User prefered option
        {
            foreach ($this->arrVotes as $Vote)
                if ($Vote->sAuthorId == $this->sUserToCheckId)
                {
                    $this->iUserVoteStatus = $Vote->iVoteValue;
                    $this->bUserVoteStarMarked = $Vote->bStarMarked;
                }
        }
    }

    protected function readCursor($p, $bEnableChildren=null)
    {
        parent::readCursor($p,$bEnableChildren);

        $this->iUserVoteStatus=0;
        $this->bUserVoteStarMarked=false;

        if (isset($p['ParentObjectId'])) $this->sParentObjectId =  (string) $p['ParentObjectId'];

        $this->getUserAnswers();

        if (isset($p['VoteCountValue'])) $this->iVoteCountValue = $p['VoteCountValue'];
        else $this->recalculateVoteCountValue();

    }

    public function findUserAnswer($sAuthorId)
    {
        foreach ($this->arrVotes as $Vote)
            if ($Vote->sAuthorId == $sAuthorId)
                return $Vote;
        return null;
    }

    protected function serializeProperties()
    {
        $arrResult = parent::serializeProperties();

        if ($this->iVoteCountValue != 0) $arrResult = array_merge($arrResult,array("VoteCountValue"=>$this->iVoteCountValue));
        if (($this->bStoreParentObjectId)&&($this->sParentObjectId != 0)) $arrResult = array_merge($arrResult,array("ParentObjectId"=>new MongoId($this->sParentObjectId)));

        return $arrResult;
    }

    public function addUserAnswer($sAuthorID, $bUpState, $bDownState, $bStarMarkedState, $bUpdate=true)
    {

        if (($bUpState==false) && ($bDownState==false) &&($bStarMarkedState==false)) return false;

        /*var_dump($bUpState);
        var_dump($bDownState);
        var_dump($bStarMarkedState);*/

        $objVoteUserAnswer = $this->findUserAnswer($sAuthorID);
        if ($objVoteUserAnswer != null)
        {
            $this->iVoteCountValue -= $objVoteUserAnswer->iVoteValue;
            $objVoteUserAnswer->rewriteStatus($bUpState, $bDownState, $bStarMarkedState);
            $this->changeVoteValueAndRankIt($this->iVoteCountValue + $objVoteUserAnswer->iVoteValue);
        } else//never voted
        {
            $objVoteUserAnswer = new Vote_answer_model(true);
            $objVoteUserAnswer->rewriteStatus($bUpState, $bDownState, $bStarMarkedState);
            $objVoteUserAnswer->sAuthorId = $sAuthorID;
            array_push($this->arrVotes,$objVoteUserAnswer);
            $this->changeVoteValueAndRankIt($this->iVoteCountValue + $objVoteUserAnswer->iVoteValue);
        }

        if (($bUpdate)&&($objVoteUserAnswer != null))
            $this->storeUpdate();

        return $objVoteUserAnswer;
    }

    public function recalculateVoteCountValue()
    {
        $this->iVoteCountValue=0;
        foreach ($this->arrVotes as $Vote)
            $this->iVoteCountValue += $Vote->iVoteValue;

        //rank the algorithms
        $this->rankAlgorithmSimple();
    }

    public function changeVoteValueAndRankIt($iNewVoteCountValue)
    {
        if ($iNewVoteCountValue != $this->iVoteCountValue)
        {
            $this->iVoteCountValue = $iNewVoteCountValue;
            $this->rankAlgorithmSimple();
        }
    }

    /* we suppose there was a voting event of this object,
   so the the algorithm will rerank the children of the parents that are up for this object */
    public function rankAlgorithmSimple($sChildrenArrayName=null)
    {
        if ($sChildrenArrayName == null) $sChildrenArrayName = $this->sDefaultChildrenArrayName;

        $object = $this->hierarchyParent;
        $objParent = $object->hierarchyParent;
        if ($objParent == null) return;// no ranking ?
        if (isset($objParent->{$sChildrenArrayName}) == false)  return;//no parents's children

        $iIndexPosition = -1 ;
        for ($index=0; $index < count($objParent->{$sChildrenArrayName}); $index++ )
        {
            $currentObject = $objParent->{$sChildrenArrayName}[$index];
            if ($currentObject == $object)
            {
                $iIndexPosition=$index;
                break;
            }
        }

        /*  22 compareObject
            23 object
            i *(22 - 23) >= 1             i=-1

            15 //object
            17 //compare object
            i*(17-15) >= 1                i=1 */

        $this->moveToPreviousParent = null; $this->moveToPreviousParentAction =0;
        if ($iIndexPosition != -1)
        {
            $bChange=true;
            while ($bChange)//we still have elements to compare
            {
                $bChange=false;
                for ($iOffset = -1; $iOffset <=1; $iOffset+=2 )
                {
                    if (($iIndexPosition + $iOffset >= 0)  && ( $iIndexPosition + $iOffset < count($objParent->{$sChildrenArrayName})))
                    {
                        $compareObject = $objParent->{$sChildrenArrayName}[$iIndexPosition + $iOffset];
                        if ($iOffset * ($compareObject->objVote->iVoteCountValue - $object->objVote->iVoteCountValue) >= 1) {
                            $temp = $objParent->{$sChildrenArrayName}[$iIndexPosition + $iOffset];
                            $objParent->{$sChildrenArrayName}[$iIndexPosition + $iOffset] = $objParent->{$sChildrenArrayName}[$iIndexPosition];
                            $objParent->{$sChildrenArrayName}[$iIndexPosition] = $temp;
                            $bChange = true;

                            if ($iOffset == -1) {
                                //echo $iOffset;
                                //echo $iIndexPosition - 2;
                                $this->moveToPreviousParent = $temp;
                                $this->moveToPreviousParentAction = $iOffset;
                                /*if ($iIndexPosition - 2 >= 0)
                                    $this->moveToPreviousParent = $objParent->{$sChildrenArrayName}[$iIndexPosition - 2];
                                else $this->moveToPreviousParent = $objParent;*/
                            }
                            else
                                $this->moveToPreviousParent = $temp;
                                $this->moveToPreviousParentAction = $iOffset;
                                /*
                                if ($iIndexPosition+1 < count($objParent->{$sChildrenArrayName}))
                                    $this->moveToPreviousParent = $objParent->{$sChildrenArrayName}[$iIndexPosition+1];
                                else $this->moveToPreviousParent = $objParent;*/
                        }
                    }
                }
            }
        }

    }

    public function cloneObject($object)
    {
        parent::cloneObject($object); // TODO: Change the autogenerated stub
        $this->iVoteCountValue = $object->iVoteCountValue;
        $this->sGrandParentObjectId = $object->sGrandParentObjectId;
        $this->sParentObjectId = $object->sParentObjectId;

        foreach ($object->arrVotes as $voteElement)
        {
            $voteElementNew = new Vote_answer_model(true, $this->hierarchyGrandParent, $this);
            $voteElementNew->cloneObject($voteElement);
            array_push($this->arrVotes, $voteElementNew);
        }

    }

}