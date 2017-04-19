<?php

require_once APPPATH.'modules/forums/replies/reply/models/Reply_model.php';

class Replies_model extends MY_Hierarchy_page_model
{
    public $sClassName = 'Replies_model';
    public $sAttachedParentType;
    public $sTitle;

    public $iNoReplies;
    public $iNoUsersReplies;

    public $arrChildrenDefinition = array(array("Name"=>"Children","Class"=>"Reply_model","Array"=>"arrChildren","EnableChildren"=>true,"CreateVisitorsStatistics"=>true,"CreateVoting"=>true));

    public function __construct($bEnableChildren=true, $hierarchyGrandParent=null)
    {
        parent::__construct($bEnableChildren, $hierarchyGrandParent,null,true,false,false,false);

        $this->initDB('Replies',TUserRole::notLogged, TUserRole::User, TUserRole::Admin, TUserRole::SuperAdmin);

        $this->load->model('users/users_minimal','UsersMinimal');
    }

    protected function sortTopRepliesByPosts($a, $b)
    {
        return $a['order'] - $b['order'];
    }

    public function findTopRepliesByAttachedParentId($sAttachedParentId='', $iNumber=4)
    {
        return $this->loadContainerByAttachedId($sAttachedParentId,array(),true);
    }

    public function findReplyContainer($sId)
    {
        $ReplyContainer = $this->loadContainerById($sId,array(),true);
        return $ReplyContainer;
    }

    public function findAllReplies()
    {
        return $this->convertToArray($this->findAll([],true));
    }

    protected function readCursor($p, $bEnableChildren=null)
    {
        parent::readCursor($p, $bEnableChildren);

        if (isset($p['Title'])) $this->sTitle = $p['Title'];
        if (isset($p['AttachedParentType'])) $this->sAttachedParentType = $p['AttachedParentType'];

        if (isset($p['NoReplies'])) $this->iNoReplies = $p['NoReplies'];
        if (isset($p['NoUsers'])) $this->iNoUsersReplies = $p['NoUsersReplies'];
    }

    public function getNestedLevelBackgroundColor()
    {
        return '';
    }

    protected function serializeProperties($sTopicId='', $sTopicFullURL='')
    {
        $arrResult = parent::serializeProperties();

        //if (isset($p['GrandParentId'])) $this->sGrandParentId = (string) $p['GrandParentId'];
        if ($this->sTitle != '') $arrResult = array_merge($arrResult, array("Title"=>$this->sTitle));
        if ($this->sAttachedParentType != '') $arrResult = array_merge($arrResult, array("AttachedParentType" => $this->sAttachedParentType));
        //if (isset($p['ParentReplyId'])) $this->sParentReplyId = (string) $p['ParentReplyId'];

        if ($this->iNoReplies != 0) $arrResult = array_merge($arrResult, array("NoReplies"=>$this->iNoReplies));
        if ($this->iNoUsersReplies != 0) $arrResult = array_merge($arrResult, array("NoUsersReplies"=>$this->iNoUsersReplies));

        return $arrResult;
    }

    public function insertReply($newReply)
    {
        if (($newReply == null) || (!is_object($newReply)) || (get_class($newReply) != 'Reply_model')) return false;

        $this->insertChild($newReply,"arrChildren");

        $this->refreshNewReply($newReply);

        return true;
    }

    public function refreshNewReply($newReply, $childReply=null)
    {
        $this->iNoReplies++;

        if ($this->checkAuthorNew($newReply->sAuthorId, true, $childReply) == 0)
            $this->iNoUsersReplies++;
    }

    public function deleteReply($deletingReply)
    {
        if (($deletingReply == null) || (!is_object($deletingReply)) || (get_class($deletingReply) != 'Reply_model')) return false;

        $this->deleteChild($deletingReply->sID);

        $this->refreshDeletingReply($deletingReply);

        return true;
    }

    public function refreshDeletingReply($deletingReply, $childReply=null)
    {
        $this->iNoReplies--;

        if ($this->checkAuthorNew($deletingReply->sAuthorId, true, $childReply) == 1)
            $this->iNoUsersReplies--;
    }

}