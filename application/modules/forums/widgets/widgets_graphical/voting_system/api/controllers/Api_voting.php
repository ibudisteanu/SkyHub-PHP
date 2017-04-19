<?php

class Api_voting extends  MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('reply/Replies_model','RepliesModel');
        $this->load->library('StringsAdvanced',null,'StringsAdvanced');
    }

    public function processVoteSubmit()
    {
        $response = array();

        //checking the posts
        $sError = '';
        if ( !isset($_POST) )
        {
            $this->returnError ('NO POST SET');
            return;
        }

        $this->load->model('voting/Query_trials_blocked_submit_voting_too_many_times','QueryTooManyTrials');
        if (!TUserRole::checkUserRights(TUserRole::User))
        {
            $this->returnError('You are not logged in. <a href="'.base_url("register#Registration").'"><strong>Register</strong></a> or <a href="'.base_url("login#Login").'"><strong>Login</strong></a>');
            return false;
        }

        if (!$this->QueryTrials->checkIPAddress($this->QueryTooManyTrials))
        {
            $this->returnError($this->QueryTrials->sError);
            return false;
        }

        if (!isset($_POST['id']))
            $sError .= '<strong>ID</strong> not presented <br/>';

        /*if (!isset($_POST['parentObjectId']))
            $sError .= '<strong> Parent Object Id </strong> not presented <br/>';*/

        if (!isset($_POST['grandParentId']))
            $sError .= '<strong>Grand Parent ID</strong> not presented <br/>';

        if (!isset($_POST['grandParentType']))
            $sError .= '<strong>Grand Parent Type</strong> not presented <br/>';

        if (!isset($_POST['up']))
            $sError .= '<strong>UP Status</strong> not presented <br/>';

        if (!isset($_POST['down']))
            $sError .= '<strong>DOWN Status</strong> not presented <br/>';

        if (!isset($_POST['star']))
            $sError .= '<strong>STAR Status</strong> not presented <br/>';

        if ($sError != '')
        {
            $this->returnError ($sError);
            return false;
        }

        $sId = $_POST['id'];
        if (isset($_POST['parentObjectId'])) $sParentObjectId = $_POST['parentObjectId'];
        else $sParentObjectId='';
        $sGrandParentId = $_POST['grandParentId'];
        $sGrandParentType = $_POST['grandParentType'];

        $bUpStatus=false;
        switch ((string)$_POST['up'])
        {
            case '1':
            case 'true':
                $bUpStatus=true;
                break;
        }
        $bDownStatus=false;
        switch ((string)$_POST['down'])
        {
            case '1':
            case 'true':
                $bDownStatus=true;
                break;
        }
        $bStarMarkedStatus=false;

        switch ((string)$_POST['star'])
        {
            case '1':
            case 'true':
                $bStarMarkedStatus=true;
                break;
        }

        /*var_dump($bUpStatus);
        var_dump($bDownStatus);
        var_dump($bStarMarkedStatus);*/

        //Grand Parent is the Topic
        //Parent is the Reply Parent

        $objParent=null; $objVote=null;
        $objGrandParent = null; $objRepliesContainer=null;

        switch ($sGrandParentType)
        {
            case "topic" :
                $this->load->model('topics/Topics_model','TopicsModel');
                $objTopic = $this->TopicsModel->getTopic($sGrandParentId);
                $objGrandParent = $objTopic;

                if ($sGrandParentId == $sParentObjectId) //it is the topic
                {
                    if ($objTopic != null)
                        $objVote = $objTopic->objVote;
                    else
                        $sError .= '<strong>Topic parent couldn\'t be found</strong><br/>';
                } else // it is a reply
                {
                    $objRepliesContainer = $this->RepliesModel->findTopRepliesByAttachedParentId($sGrandParentId);
                    if ($objRepliesContainer == null)
                        $sError .= '<strong>Reply Container Parent couldn\'t be found by Grand Parent ID</strong><br/>';
                    else
                    {
                        if ($sParentObjectId == '')
                            $sError .= '<strong>Reply Parent Object Id </strong> not presented <br/>';
                        else
                        {
                            $objReply = $objRepliesContainer->findChild($sParentObjectId );
                            if ($objReply != null)
                                $objVote = $objReply->objVote;
                            else
                                $sError .= '<strong>Reply parent couldn\'t be found</strong><br/>';
                        }
                    }
                }
                break;
            case "vote":
                $this->load->model('voting/Voting_model','VotesModel');
                break;
            default:
                $sError .= 'No valid action<br/>';
                break;
        }

        if ($objVote == null)
            $sError .= '<strong>Parent couldn\'t be found</strong>';

        if ($sError != '')
        {
            $this->returnError($sError);
            return false;
        }

        $objVoteUserAnswer = $objVote->addUserAnswer($this->MyUser->sID,$bUpStatus,$bDownStatus,$bStarMarkedStatus);

        if ((($objVoteUserAnswer == false)||(get_class($objVoteUserAnswer)!='Vote_answer_model')))
        {
            $this->returnError('No vote stored');
            return false;
        }

        if ((isset($objGrandParent->objRepliesComponent))&&($objGrandParent->objRepliesComponent != null)) {
            //$objGrandParent->objRepliesComponent->deleteReply($objReply, $objRepliesContainer);
            $objGrandParent->objRepliesComponent->refreshReplies($objRepliesContainer);
        }

        $objGrandParent->storeUpdateOnlyChild();
        $objGrandParent->recalculateKeepSortedData();

        if ($objVoteUserAnswer != null)
        {
            $response['status'] = 'success';
            $response['message'] = 'Your vote has been stored successfully';
            $response['upState'] = (int)$bUpStatus;
            $response['downState'] = (int)$bDownStatus;
            $response['markedStarState'] = (int)$bStarMarkedStatus;
            $response['objectId'] = "replyId".(string)$objVote->hierarchyParent->sID;
            $response['moveToPreviousParentId'] = ($objVote->moveToPreviousParent <> null) ? "replyId".(string)$objVote->moveToPreviousParent->sID : '' ;
            $response['moveToPreviousParentAction'] = $objVote->moveToPreviousParentAction;
            echo json_encode($response);
            return true;
        }


        $this->returnError('The request couldn\'t be processed');

    }

    protected function returnError($sError)
    {
        $response['status'] = 'error';
        $response['message'] = $sError;
        $response['upState'] = 0;
        $response['downState'] = 0;
        $response['markedStarState'] = 0;
        echo json_encode($response);
    }


}