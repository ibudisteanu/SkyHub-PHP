<?php

class Api_replies extends  MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('topics/Topics_model','TopicsModel');
        $this->load->model('forum/Forums_model','ForumsModel');
        $this->load->model('reply/Replies_model','RepliesModel');

        $this->load->library('StringsAdvanced',null,'StringsAdvanced');
    }

    public function processReplySubmit($bEcho=true)
    {
        $response = array();

        //checking the posts
        $sError = '';
        if ( !isset($_POST) )
        {
            if ($bEcho) $this->returnError ('NO POST SET');
            return;
        }

        if (!isset($_POST['Action']))
            $sError .= '<strong>Action</strong> not presented <br/>';

        if (!isset($_POST['ParentId']))
            $sError .= '<strong>ParentId</strong> not presented <br/>';

        if (!isset($_POST['GrandParentId']))
            $sError .= '<strong>GrandParentId</strong> not presented <br/>';

        if (!isset($_POST['Title']))
            $sError .= '<strong>Reply Title</strong> not presented <br/>';

        if (!isset($_POST['MessageCode']))
            $sError .= '<strong>Message Code</strong> not presented <br/>';

        if ($sError != '')
        {
            if ($bEcho) $this->returnError ($sError);
            return false;
        }

        $sAction = $_POST['Action'];
        $sParentId = $_POST['ParentId'];

        $sGrandParentId = $_POST['GrandParentId'];
        $sTitle = $_POST['Title'];
        $sMessageCode = $_POST['MessageCode'];

        //Grand Parent is the Topic
        //Parent is the Reply Parent

        $objGrandParent = null; $objParent=null; $sGrandParentType = '';
        $iNestedLevel=0;
        switch ($sAction)
        {
            case 'edit-reply':
            case 'add-reply':
            case "delete-reply" :
                $objGrandParent = $this->TopicsModel->getTopic($sGrandParentId);

                if ($objGrandParent != null)
                    $sGrandParentType = 'topic';

                break;
            default:
                $sError .= 'No valid action<br/>';
                break;
        }

        //Check the ParentId
        /*if (strlen($sTitle) == 0)
            $sError .= 'Title is empty<br/>';*/

        if (strlen($sMessageCode) == 0 )
            $sError .= 'Message code is empty <br/>';

        if ((!isset($objGrandParent))&&($objGrandParent == null))
            $sError .= 'Grand parent not found'.$sGrandParentId.' <br/>';

        if ($sError != '')
        {
            if ($bEcho) $this->returnError($sError);
            return false;
        }


        if ($sTitle != '') $sURLName = stripslashes(rtrim($sTitle,'/'));
        else $sURLName  = '';
        $sURLName = $this->StringsAdvanced->processURLString($sURLName);


        $this->load->model('add_forum_category/query_trials_blocked_add_forum_category_too_many_times','QueryTooManyTrials');
        if (!$this->QueryTrials->checkIPAddress($this->QueryTooManyTrials))
        {
            if ($bEcho) $this->returnError($this->QueryTrials->sError);
            return false;
        }

        if (!$this->MyUser->bLogged)
        {
            $this->load->model('session_actions/Session_actions','SessionActions');
            $this->SessionActions->createSessionAction('newReply','newReplyPOST',['POST'=>$_POST]);

            $this->returnError('You are not registered',false);
            return false;
        }

        $objRepliesContainer=null;

        if ($objGrandParent->objRepliesComponent->sRepliesContainerId != '')
            $objRepliesContainer = $this->RepliesModel->findReplyContainer($objGrandParent->objRepliesComponent->sRepliesContainerId);


        if ($objRepliesContainer == null)
        {
            if (isset($objGrandParent->sName)) $sName = $objGrandParent->sName;
            else
                if (isset($objGrandParent->sTitle)) $sName = $objGrandParent->sTitle;
                else $sName = '';

            $objRepliesContainer = new Replies_model(true,null);
            $objRepliesContainer->sTitle = $sName;
            $objRepliesContainer->sAttachedParentType = $sGrandParentType;
            $objRepliesContainer->sAttachedParentId = $objGrandParent->sID;
            $sRepliesContainerId = $objRepliesContainer->storeUpdate();


            $objGrandParent->objRepliesComponent->sRepliesContainerId = (string) $sRepliesContainerId;

            $objGrandParent->storeUpdateOnlyChild();
        }


        if ($sGrandParentId != $sParentId)//parent is a reply
        {
            $objParent = $objRepliesContainer->findChild($sParentId);

            if ($sParentId == $objRepliesContainer->sID) $objParent = $objRepliesContainer;

            if ($objParent != null) $iNestedLevel = $objParent->iNestedLevel+1;
        } else//parent is the  topic
        {
            $objParent = $objRepliesContainer;
        }

        if (($sAction=='edit-reply')||($sAction == "delete-reply" ))
        {
            //search for the children
            $currentReplyForUpdate = $objParent;
            if (($currentReplyForUpdate== null)||($currentReplyForUpdate->getAttachedGrandParentId()!=$objGrandParent->sID))
            {
                $this->returnError('Invalid Id to Update');
                return false;
            }

            if (!$currentReplyForUpdate->checkOwnership())
            {
                $this->returnError('You don\'t have enough rights edit this Reply');
                return false;
            }
        }

        $this->ViewReplyController = modules::load('reply/View_reply');
        switch ($sAction)
        {
            case 'add-reply':
                $replyNewCreated = new Reply_model(true);
                $replyNewCreated->sURLName = $objParent->sURLName;
                $replyNewCreated->sFullURLLink = rtrim($objGrandParent->sFullURLLink,'/').'/'.$replyNewCreated->sID;
                $replyNewCreated->sFullURLName = rtrim($objGrandParent->sFullURLName,'/').'/'.$sTitle;
                $replyNewCreated->sFullURLDomains = rtrim($objGrandParent->sFullURLDomains,'/').'/'.'reply';
                $replyNewCreated->iNestedLevel = $iNestedLevel;
                $replyNewCreated->sAuthorId=$this->MyUser->sID;
                $replyNewCreated->sTitle = $sTitle;
                $replyNewCreated->setMessageCode($sMessageCode);


                //echo count($objParent->arrChildren);
                $objParent->insertReply($replyNewCreated);
                //echo count($objParent->arrChildren);

                if ($replyNewCreated->storeUpdate()) {

                    $this->load->model('counter/counter_statistics','CounterStatistics');
                    $this->CounterStatistics->increaseComments(1);

                    if ((isset($objGrandParent->objRepliesComponent))&&($objGrandParent->objRepliesComponent != null)) {
                        $objGrandParent->objRepliesComponent->iNoReplies = $objRepliesContainer->iNoReplies;
                        $objGrandParent->objRepliesComponent->iNoUsersReplies = $objRepliesContainer->iNoUsersReplies;
                        //$objGrandParent->objRepliesComponent->newReply($replyNewCreated);
                        $objGrandParent->objRepliesComponent->refreshReplies($objRepliesContainer);

                        $objGrandParent->storeUpdateOnlyChild();
                        $objGrandParent->recalculateKeepSortedData();
                    }



                    if ($bEcho) $this->returnSuccess('Your message has been posted successfully', $this->ViewReplyController->renderReply($replyNewCreated, null), $replyNewCreated->sID, $replyNewCreated->objVote->sID);
                }

                return true;
            case 'edit-reply':
                if (($currentReplyForUpdate->sTitle != $sTitle) || ($currentReplyForUpdate->getMessageCodeRendered() != $sMessageCode)) {
                    $currentReplyForUpdate->sTitle = $sTitle;
                    $currentReplyForUpdate->setMessageCode($sMessageCode);
                    $currentReplyForUpdate->bLastChanged = true;
                }

                if ($currentReplyForUpdate->storeUpdate())
                {
                    $currentReplyForUpdate->reload();

                    if ((isset($objGrandParent->objRepliesComponent))&&($objGrandParent->objRepliesComponent != null)) {
                        $objGrandParent->objRepliesComponent->iNoReplies = $objRepliesContainer->iNoReplies;
                        $objGrandParent->objRepliesComponent->iNoUsersReplies = $objRepliesContainer->iNoUsersReplies;
                        //$objGrandParent->objRepliesComponent->editReply($currentReplyForUpdate);
                        $objGrandParent->objRepliesComponent->refreshReplies($objRepliesContainer);

                        $objGrandParent->storeUpdateOnlyChild();
                        $objGrandParent->recalculateKeepSortedData();
                    }

                    if ($bEcho) $this->returnSuccess('Your message has been updated successfully',$this->ViewReplyController->renderReply($currentReplyForUpdate,null),$currentReplyForUpdate->sID, $currentReplyForUpdate->objVote->sID);
                }
                return true;
            case 'delete-reply':
                $response['ParentId'] = (string)$currentReplyForUpdate->getParentId();
                $objParent = $currentReplyForUpdate->hierarchyParent;

                $sCurrentlyReplyForUpdateId = $currentReplyForUpdate->sID;
                $objParent ->deleteReply($currentReplyForUpdate);

                $response['NoSubReplies'] = (count($objParent->arrChildren));
                $objRepliesContainer->storeUpdate();

                $this->load->model('counter/counter_statistics','CounterStatistics');
                $this->CounterStatistics->increaseComments(-1);

                if ((isset($objGrandParent->objRepliesComponent))&&($objGrandParent->objRepliesComponent != null)) {
                    $objGrandParent->objRepliesComponent->iNoReplies = $objRepliesContainer->iNoReplies;
                    $objGrandParent->objRepliesComponent->iNoUsersReplies = $objRepliesContainer->iNoUsersReplies;
                    //$objGrandParent->objRepliesComponent->deleteReply($sCurrentlyReplyForUpdateId, $objRepliesContainer);
                    $objGrandParent->objRepliesComponent->refreshReplies($objRepliesContainer);

                    $objGrandParent->storeUpdateOnlyChild();
                    $objGrandParent->recalculateKeepSortedData();
                }

                $response['status'] = 'success';
                $response['message'] = "The comment has been deleted";
                if ($bEcho) echo json_encode($response);
                return true;
            default:
                $this->returnError('The request was rejected');
                return false;
        }

        $this->returnError('The request couldn\'t be processed');

    }

    protected function returnError($sError, $bLogged=true)
    {
        $response['status'] = 'error';
        $response['message'] = $sError;
        $response['logged'] = $bLogged;
        echo json_encode($response);
    }

    protected function returnSuccess($sSuccess, $sBlockHTMLCode='', $sBlockHTMLTagId='', $sVotingId='')
    {
        $response['status'] = 'success';
        $response['message'] = $sSuccess;
        $response['BlockHTMLCode'] = $sBlockHTMLCode;
        $response['BlockHTMLTagId'] = (string)$sBlockHTMLTagId;
        $response['sVotingId'] = (string) $sVotingId;
        echo json_encode($response);
    }



}