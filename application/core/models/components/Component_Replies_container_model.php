<?php

/**
 * COMPONENT that STORE INFORMATION in TOPICS, ARTICLES about TOP COMMENTS, LAST COMMENTS
 */

class Replies_container_model extends CI_Model
{
    private $parent;

    public $sRepliesContainerId;
    public $iNoReplies;
    public $iNoUsersReplies;

    public $arrTopReplies;
    public $arrLastReplies;

    public $ViewReplyController;


    public function __construct($parent)
    {
        parent::__construct();

        $this->parent = $parent;

        if (!isset($this->iNoReplies)) $this->iNoReplies = 0 ;
        if (!isset($this->iNoUsersReplies)) $this->iNoUsersReplies  = 0;

        $this->load->library('TimeLibrary',null,'TimeLibrary');
        $this->load->model('reply/reply_model','ReplyModel');
        $this->load->model('reply/Replies_model','RepliesModel');

        //calling and cached replies
        if ((is_array($this->arrTopReplies))&&(count($this->arrTopReplies)>0))
            foreach ($this->arrTopReplies as $topReply) {
                $topReply->__construct();
                $topReply->sHierarchyGrandParentId = $this->parent->sID;

            }

        if ((is_array($this->arrLastReplies))&&(count($this->arrLastReplies)>0))
            foreach ($this->arrLastReplies as $lastReply) {
                $lastReply->__construct();
                $lastReply->sHierarchyGrandParentId = $this->parent->sID;
            }

//        if ((is_array($this->arrTopReplies))&&(count($this->arrTopReplies)>0)) {
//
//            //foreach ($this->arrTopReplies as $topReply) {
//                /*$topReply = $this->arrTopReplies[$index];
//                //unset($topReply->__PHP_Incomplete_Class_Name);
//                //$this->objectToObject($topReply,'Reply_model');
//                $topReply->__construct();*/
//            for ($index=0; $index<count($this->arrTopReplies); $index++) {
//                $this->arrTopReplies[$index] = $this->cloneReply($this->arrTopReplies[$index], $this->arrTopReplies);
//            }
//        }
//
//        if ((is_array($this->arrLastReplies))&&(count($this->arrLastReplies)>0))
//            for ($index=0; $index<count($this->arrLastReplies); $index++) {
//
//                /*$bFound=false;
//                for ($indexTop=0; $indexTop<count($this->arrTopReplies); $indexTop++)
//                    if ($this->arrTopReplies[$indexTop]->sID == $this->arrLastReplies[$index]->sID) {
//                        $bFound = true;
//                        $this->arrLastReplies[$index] = $this->arrTopReplies[$indexTop];
//                        break;
//                    }
//
//                if (!$bFound)*/
//                    $this->arrLastReplies[$index] = $this->cloneReply($this->arrLastReplies[$index], $this->arrLastReplies);
//
//            }


        if ((!isset($this->arrTopReplies)) || (!isset($this->arrTopReplies)) || (count($this->arrTopReplies) == 0) || (count($this->arrLastReplies) == 0))
            $this->refreshReplies();

    }

    public function readCursor($p, $bEnableChildren=null)
    {
        if (isset($p['RepliesContainerId'])) $this->sRepliesContainerId = (string) $p['RepliesContainerId'];

        if (isset($p['NoReplies'])) $this->iNoReplies = $p['NoReplies'];

        if (isset($p['NoUsersReplies'])) $this->iNoUsersReplies = $p['NoUsersReplies'];

        if ((!isset($this->arrTopReplies)) || (!isset($this->arrTopReplies)) || ($this->arrLastReplies == []) || ($this->arrTopReplies == []))
            $this->refreshReplies();

        //just caching
        /*if (isset($p['LastReplies'])) $this->arrLastReplies = $p['LastReplies'];
        if (isset($p['TopReplies'])) $this->arrLastReplies = $p['TopReplies'];*/
    }

    public function serializeProperties()
    {
        $arrResult = [];
        if (isset($this->sRepliesContainerId)) $arrResult = array_merge($arrResult, array("RepliesContainerId"=>New MongoId($this->sRepliesContainerId)));
        if ((isset($this->iNoReplies))&&($this->iNoReplies != 0)) $arrResult = array_merge($arrResult, array("NoReplies"=>$this->iNoReplies));
        if ((isset($this->iNoUsersReplies))&&($this->iNoUsersReplies != 0)) $arrResult = array_merge($arrResult, array("NoUsersReplies"=>$this->iNoUsersReplies));

        //just caching
        /*if ((isset($this->arrLastReplies)) && ($this->arrLastReplies != [])) $arrResult = array_merge($arrResult, array("LastReplies"=>$this->arrLastReplies));*/

        return $arrResult;
    }

    public function newCommentsForUser()
    {
        if ($this->MyUser->bLogged) {

            $activity = $this->MyUser->UserActivities->getFastTopicActivity($this->parent->sID);
            if ($activity == null)
                return $this->iNoReplies;

            if ($activity->dtLastActivity == null) return ['replies'=>$this->iNoReplies,"visited"=>false];

            $dateLastActivity = $this->TimeLibrary->convertMongoDateToTime($activity->dtLastActivity);

            $iNewRepliesCount = 0;
            foreach ($this->arrLastReplies as $reply)
                if (($reply!=null)&&(isset($reply->dtCreationDate))&&($reply->dtCreationDate != null))
                {
                    $dateReply = $this->TimeLibrary->convertMongoDateToTime($reply->dtCreationDate);
                    if ($dateLastActivity < $dateReply)
                        $iNewRepliesCount ++;

                }

            return ['replies'=>$iNewRepliesCount,"visited"=>true];
        }
        return ['replies'=>$this->iNoReplies,"visited"=>false];
    }

    protected function checkNested($clonedReply, $originalReply, &$arrayReplies)
    {
        $parent = $originalReply;
        while ($parent!=null)
        {
            foreach ($arrayReplies as $elementReply)
                if (($elementReply!=null)&&($elementReply->sID == $parent->sID))
                    if (!$elementReply->checkReplyChildExists($clonedReply, true)) {
                        $clonedReply->bHideReplyNestedAlready=true;
                        $elementReply->insertChild($clonedReply);
                    }

            /*foreach ($this->arrLastReplies as $lastReply)
                if (($lastReply!=null)&&($lastReply->sID == $parent->sID))
                    if (!$lastReply->checkReplyChildExists($clonedReply, true)) {
                        $clonedReply->bHideReplyNestedAlready=true;
                        $lastReply->insertChild($clonedReply);
                    }

            foreach ($this->arrTopReplies as $topReply)
                if (($topReply!=null)&&($topReply->sID == $parent->sID))
                    if (!$topReply->c0heckReplyChildExists($clonedReply, true)) {
                        $clonedReply->bHideReplyNestedAlready=true;
                        $topReply->insertChild($clonedReply);
                    }*/

            if ($parent->hierarchyParent != $parent)
                $parent = $parent->hierarchyParent;
        }
    }

    public function cloneReply($originalReply, &$arrayReplies)
    {
        try {
            foreach ($arrayReplies as $reply)
                if ($reply->sID == $originalReply->sID)
                    return $reply;

//        foreach ($this->arrLastReplies as $lastReply)
//            if ($lastReply->sID == $originalReply->sID)
//                return $lastReply;
//
//        foreach ($this->arrTopReplies as $topReply)
//            if ($topReply->sID == $originalReply->sID)
//                return $topReply;

            $reply = new Reply_model();
            $reply->sHierarchyGrandParentId = $this->parent->sID;

            $reply->cloneObject($originalReply);

            $this->checkNested($reply, $originalReply, $arrayReplies);

            return $reply;
        }
        catch (Exception $ex)
        {

        }
    }

    protected function findReplyExists($newReply, &$arrReplies)
    {
        foreach ($arrReplies as $reply)
            if (($reply != null)&&($reply->sID == $newReply->sID))
                return true;

        return false;
    }

    public function newReply($newReply=null)
    {
        $g_iLastRepliesMax = 4;
        $g_iTopRepliesMax = 4;

        if ($newReply==null) return false;

        $minutesNewReply = $this->TimeLibrary->getTimeDifferenceDateAndNowInMinutes($newReply->dtCreationDate);

/*        if (count($this->arrLastReplies) >= $g_iLastRepliesMax)
        {
            $arrOldestReply = [];
            for ($index=0; $index<count($this->arrLastReplies); $index++)
            {
                $lastReply = $this->arrLastReplies[$index];

                $minutesReply = $this->TimeLibrary->getTimeDifferenceDateAndNowInMinutes($lastReply->dtCreationDate);
                if (($minutesNewReply < $minutesReply) &&//newer than the current reply
                    (($arrOldestReply==[])||($arrOldestReply['minutes'] < $minutesReply)))
                {
                    $arrOldestReply['minutes']=$minutesReply;
                    $arrOldestReply['index'] = $index;
                }
            }

            if ($arrOldestReply != [])
                unset($this->arrLastReplies[$arrOldestReply['index']]);
        }*/

        if (!$this->findReplyExists($newReply,$this->arrLastReplies))
        {
            $bDone = false;
            for ($index = 0; $index < count($this->arrLastReplies); $index++) {
                $lastReply = $this->arrLastReplies[$index];
                $minutesReply = $this->TimeLibrary->getTimeDifferenceDateAndNowInMinutes($lastReply->dtCreationDate);
                if ($minutesReply > $minutesNewReply) {
                    array_splice($this->arrLastReplies, $index, 0, [$this->cloneReply($newReply, $this->arrLastReplies)]);
                    $bDone = true;
                    break;
                }
            }
            if ((count($this->arrLastReplies) > $g_iLastRepliesMax - ($bDone == false ? 1 : 0)))
                while (count($this->arrLastReplies) > $g_iLastRepliesMax - ($bDone == false ? 1 : 0)) {
                    $reply = array_pop($this->arrLastReplies);
                    foreach ($reply->arrChildren as $element) {
                        $element->removeParent();
                        unset($element->bHideReplyNestedAlready);
                    }
                }

            if ($bDone == false)
                array_push($this->arrLastReplies, $this->cloneReply($newReply, $this->arrLastReplies));
        }

        $iVoteNewReply = $newReply->objVote->iVoteCountValue;

/*      if (count($this->arrTopReplies) >= $g_iTopRepliesMax)
        {
            $arrOldestReply = [];
            for ($index=0; $index<count($this->arrTopReplies); $index++)
            {
                $topReply = $this->arrTopReplies[$index];

                $iVoteReply = $topReply->objVote->iVoteCountValue;
                if (($iVoteNewReply > $iVoteReply) &&//newer than the current reply
                    (($arrOldestReply==[])||($arrOldestReply['votes'] < $iVoteReply)))
                {
                    $arrOldestReply['votes']=$iVoteReply;
                    $arrOldestReply['index'] = $index;
                }
            }

            if ($arrOldestReply != [])
                unset($this->arrTopReplies[$arrOldestReply['index']]);
        }*/

        if (!$this->findReplyExists($newReply,$this->arrTopReplies))
        {
            $bDone = false;
            for ($index = 0; $index < count($this->arrTopReplies); $index++) {
                if ($this->arrTopReplies[$index]->objVote->iVoteCountValue > $iVoteNewReply) {
                    array_splice($this->arrTopReplies, $index, 0, [$this->cloneReply($newReply, $this->arrTopReplies)]);
                    $bDone = true;
                    break;
                }
            }
            if ((count($this->arrTopReplies) > $g_iTopRepliesMax - ($bDone == false ? 1 : 0)))
                while (count($this->arrTopReplies) > $g_iTopRepliesMax - ($bDone == false ? 1 : 0)) {
                    $reply = array_pop($this->arrTopReplies);
                    foreach ($reply->arrChildren as $element) {
                        $element->removeParent();
                        unset($element->bHideReplyNestedAlready);
                    }
                }

            if ($bDone == false)
                array_push($this->arrTopReplies, $this->cloneReply($newReply, $this->arrTopReplies));
        }
    }

    public function editReply($newReply=null, $objRepliesContainer=null)
    {
        if ($newReply==null) return false;

        for ($index=0; $index < count($this->arrLastReplies); $index++)
            if ($this->arrLastReplies[$index]->sID == $newReply->sID) {
                $this->arrLastReplies[$index] = $this->cloneReply($newReply,$this->arrLastReplies);
            }

        for ($index=0; $index < count($this->arrTopReplies); $index++)
            if ($this->arrTopReplies[$index]->sID == $newReply->sID){
                $this->arrTopReplies[$index] = $this->cloneReply($newReply, $this->arrTopReplies);
            }

        $this->refreshReplies($objRepliesContainer);
    }

    public function deleteReply($replyId='', $objRepliesContainer=null)
    {
        if ($replyId=='') return false;

        $indexLastReplies = []; $indexTopReplies = [];

        for ($index=0; $index < count($this->arrLastReplies); $index++) {
            $lastReply = $this->arrLastReplies[$index];
            if ($lastReply->sID == $replyId)
                array_push($indexLastReplies, $index);
        }

        for ($index=0; $index < count($this->arrTopReplies); $index++) {
            $topReply = $this->arrTopReplies[$index];
            if ($topReply->sID == $replyId)
                array_push($indexTopReplies, $index);
        }

        if ((count ($indexLastReplies) > 0) || (count($indexTopReplies) > 0))
        {
            foreach ($indexLastReplies as $index)
                unset($this->arrLastReplies[$index]);

            foreach ($indexTopReplies as $index )
                unset($this->arrTopReplies[$index]);

            foreach ($this->arrLastReplies as $reply) $reply->findChild($replyId,true); //delete
            foreach ($this->arrTopReplies as $reply) $reply->findChild($replyId,true); //delete

            $this->refreshReplies($objRepliesContainer);
        }
    }

    public function refreshReplies($objRepliesContainer=null)
    {
        if (!isset($this->arrLastReplies)) $this->arrLastReplies = [];
        if (!isset($this->arrTopReplies)) $this->arrTopReplies = [];

        if ($objRepliesContainer == null)
        {
            if ($this->sRepliesContainerId == '') return null;

            $this->load->model('reply/Replies_model','RepliesModel');
            $objRepliesContainer = $this->RepliesModel->findReplyContainer($this->sRepliesContainerId);
        }

        if ($objRepliesContainer  == null){
            return false; //NO REPLIES CONTAINER
        }

        //temporary solution resetting all the current options
        $this->arrLastReplies = []; $this->arrTopReplies = [];

        foreach ($objRepliesContainer->arrChildren as $replyChild)
            $this->processRefreshReplies($replyChild);

    }

    public function processRefreshReplies($replyChild)
    {
        $this->newReply($replyChild);

        foreach ($replyChild->arrChildren as $newReplyChild)
            $this->processRefreshReplies($newReplyChild);
    }

    protected function showLastComments()
    {
        //if (count($this->arrLastReplies)==0) return '';
        $sContent = '<ul class="timeline" style="margin-bottom: 0; margin-right: -50px;" >';

        $this->ViewReplyController = modules::load('reply/View_reply');

        foreach ($this->arrLastReplies as $lastReply)
            if((!isset($lastReply->bHideReplyNestedAlready))||($lastReply->bHideReplyNestedAlready==false))
                $sContent .= '<a href="">' . $this->ViewReplyController->renderReply($lastReply, null, true, true) . '</a>';


        $sContent .= '<li id="repliesNewContainer'.$this->parent->sID.'" style="overflow: hidden"> </li>';
        $sContent .= '</ul>';
        return $sContent;
    }

    protected function showTopComments()
    {
        //if (count($this->arrTopReplies) == 0) return '';
        $sContent = '<ul class="timeline" style="margin-bottom: 0; margin-right: -50px; " >';

        $this->ViewReplyController = modules::load('reply/View_reply');

        foreach ($this->arrTopReplies as $topReply)
            if((!isset($topReply->bHideReplyNestedAlready))||($topReply->bHideReplyNestedAlready==false))
                $sContent .= $this->ViewReplyController->renderReply($topReply, null, true, true);

        $sContent .= '<li id="repliesNewContainer'.$this->parent->sID.'" style="overflow: hidden"> </li>';
        $sContent .= '</ul>';
        return $sContent;
    }

    public function showInterestingComments()
    {

        return $this->showLastComments();//SHOWING ONLY THE LAST COMMENTS

        if ($this->MyUser->bLogged) {

            $activity = $this->MyUser->UserActivities->getFastTopicActivity($this->parent->sID);
            if ($activity == null)
                return $this->showTopComments();

            $dateLastActivity = $this->TimeLibrary->convertMongoDateToTime($activity->dtLastActivity);

            return $this->showLastComments();
        }

        return $this->showTopComments();

    }

}