<?php

require_once APPPATH.'core/models/MY_Hierarchy_page_model.php';

class Reply_model extends MY_Hierarchy_page_model
{
    public $sClassName = 'Reply_model';

    public $iNestedLevel=0;

    public $sTitle;
    private $sMessageCode;
    private $sMessageCodeRendered;

    public $iNoSubReplies;
    public $iNoUsersSubReplies;

    public $arrChildrenDefinition = array(array("Name"=>"Children","Class"=>"Reply_model","Array"=>"arrChildren","EnableChildren"=>true,"CreateVisitorsStatistics"=>false,"CreateVoting"=>true));

    public function __construct($bEnableChildren=true, $hierarchyGrandParent = null, $hierarchyParent=null, $bCreateVisitorsStatistics=false, $bCreateVoting=true)
    {
        $this->FeaturesSubChildren = $this->arrChildrenDefinition[0];

        parent::__construct($bEnableChildren,$hierarchyGrandParent, $hierarchyParent,  $bCreateVisitorsStatistics, $bCreateVoting);

        $this->initDB('Replies', TUserRole::notLogged, TUserRole::User, TUserRole::Admin, TUserRole::SuperAdmin);

        $this->load->library('StringEmojisProcessing',null,'StringEmojisProcessing');
    }

    public function setMessageCode($sMessageCode)
    {
        if ($this->sMessageCode != $sMessageCode) {
            $this->sMessageCode = $sMessageCode;

            if (!isset($this->StringEmojisProcessing)) $this->load->library('StringEmojisProcessing',null,'StringEmojisProcessing');

            $this->StringEmojisProcessing->processForPlainTextEmojis($this->sMessageCode, true);
            $this->StringEmojisProcessing->removeEmojisImages($this->sMessageCode);
            $this->sMessageCodeRendered = '';
        }
    }

    public function getMessageCodeRendered()
    {
        if ($this->sMessageCodeRendered == '')
        {
            $this->sMessageCodeRendered = $this->sMessageCode;

            if (!isset($this->StringEmojisProcessing)) $this->load->library('StringEmojisProcessing',null,'StringEmojisProcessing');

            $this->StringEmojisProcessing->renderEmojisImages($this->sMessageCodeRendered);
        }
        return $this->sMessageCodeRendered;
    }

    protected function readCursor($p, $bEnableChildren=null)
    {
        parent::readCursor($p,$bEnableChildren);

        if (isset($p['NestedLevel'])) $this->iNestedLevel = $p['NestedLevel'];
        //if (isset($p['ParentReplyId'])) $this->sParentReplyId = (string) $p['ParentReplyId'];

        if (isset($p['Title'])) $this->sTitle = $p['Title'];

        if (isset($p['MessageCode']))  $this->sMessageCode = $p['MessageCode'];

        if (isset($p['NoSubReplies'])) $this->iNoSubReplies = $p['NoSubReplies'];
        if (isset($p['NoUsers'])) $this->iNoUsersSubReplies = $p['NoUsersSubReplies'];

    }

    protected function serializeProperties()
    {
        $arrResult = parent::serializeProperties();

        if ($this->iNestedLevel != 0) $arrResult = array_merge($arrResult, array("NestedLevel"=>$this->iNestedLevel));
        //if (isset($p['GrandParentId'])) $this->sGrandParentId = (string) $p['GrandParentId'];
        //if (isset($p['ParentReplyId'])) $this->sParentReplyId = (string) $p['ParentReplyId'];
        //if ($this->sVotingId != '') $arrResult = array_merge($arrResult, array("VotingId"=>$this->sVotingId));

        if ($this->sTitle != '') $arrResult = array_merge($arrResult, array("Title"=>$this->sTitle));
        if ($this->sMessageCode != '')
        {
            $this->StringEmojisProcessing->removeEmojisImages($this->sMessageCode);
            $arrResult = array_merge($arrResult, array("MessageCode"=>$this->sMessageCode));
        }

        if ($this->iNoSubReplies != 0) $arrResult = array_merge($arrResult, array("NoSubReplies"=>$this->iNoSubReplies));
        if ($this->iNoUsersSubReplies != 0) $arrResult = array_merge($arrResult, array("NoUsersSubReplies"=>$this->iNoUsersSubReplies));

        return $arrResult;
    }

    protected function loadFromCursor($cursor, $bEnable=true)
    {
        if (!is_array($cursor))
            foreach ($cursor as $p)
                $this->readCursor($p);
        else
            $this->readCursor($cursor);

 /*       if (($this->sFullURLLink == '')&&(TUserRole::checkCompatibility($this->MyUser, TUserRole::Admin)))//Not URLFullName assigned
        {
            $this->sFullURLLink = $this->calculateFullURL();
            $this->update(array("_id"=>new MongoId($this->sID)) ,array("FullURLLink"=>$this->sFullURLLink));
        }
        if ($this->sFullURLLink) return true;*/

        return true;
    }

    public function insertReply($newReply)
    {
        if (($newReply == null) || (!is_object($newReply)) || (get_class($newReply) != 'Reply_model')) return false;

        $this->insertChild($newReply,"arrChildren");

        $this->refreshNewReply($newReply);

        $this->sendNewReplyNotification($newReply);

        return true;
    }

    public function refreshNewReply($newReply, $childReply=null)
    {
        $this->iNoSubReplies++;

        if ($this->checkAuthorOccurrence($newReply->sAuthorId, true, $childReply) == 0)
            $this->iNoUsersSubReplies++;

        if ($this->hierarchyParent != null) $this->hierarchyParent->refreshNewReply($newReply, $this);
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
        $this->iNoSubReplies--;

        if ($this->checkAuthorOccurrence($deletingReply->sAuthorId, true, $childReply) == 1)
            $this->iNoUsersSubReplies--;

        if ($this->hierarchyParent != null) $this->hierarchyParent->refreshDeletingReply($deletingReply, $this);
    }

    public function getTitleForIdName()
    {
        $this->load->library('StringsAdvanced',null,'StringsAdvanced');
        return $this->StringsAdvanced->getStringForIdName($this->sTitle);
    }

    public function getURL()
    {
        $sURL = rtrim($this->sURLName,'/');
        $iPos = strrpos($sURL,'/');
        if ($iPos !== false)
            $sURL[$iPos] = '#';

        return base_url('topic/'.$sURL);
    }

    public function getFullURL()
    {
        $sFullURLLink = rtrim($this->sFullURLLink,'/');
        $iPos = strrpos($sFullURLLink,'/');
        if ($iPos !== false)
            $sFullURLLink[$iPos] = '#';

        return base_url('topic/'.$sFullURLLink);
    }

    public function getUsedURL()
    {
        return $this->getFullURL();
    }

    public function calculateFullURL()
    {
        /*if ($this->sParentCategoryId == '') return false;

        $this->load->model('topics/Topics_model','TopicsModel');
        $Topic = $this->TopicsModel->findTopic($this->sParentTopicId,'');

        $sFullURL = rtrim($Topic->sFullURLLink,'/').'/'.$this->sURLName;
        return $sFullURL;*/
    }

    public function getNestedLevelBackgroundColor()
    {
        switch ($this->iNestedLevel)
        {
            case 0: return '';
            case 1: return 'background-color: rgb(250,250,250)';
            case 2: return 'background-color: rgb(245,245,245)';
            case 3: return 'background-color: rgb(240,240,240)';
            case 4: return 'background-color: rgb(235,235,235)';
            case 5: return 'background-color: rgb(230,230,230)';
            case 6: return 'background-color: rgb(225,225,225)';
            case 7: return 'background-color: rgb(220,220,220)';
        }
    }

    public function findTopSubReplies($iNumber=4)
    {
    }

    public function sendNewReplyNotification($newReply)
    {
        $sReplyTitle = $newReply->sTitle; $sReplyText = $newReply->getMessageCodeRendered(); $sReplyURL = $newReply->getUsedURL();

        $arrAuthors =[];
        $this->getAuthorsInvolved($arrAuthors, true);

        foreach ($arrAuthors  as $author)
            modules::load('user_notifications/add_notifications_controller')->addNotificationFromUser($author, $sReplyTitle.'<br/><br/>'.$sReplyText, '<b>'.$this->MyUser->getFullName().'</b> replied : '.$sReplyTitle, $this->MyUser->sID, $sReplyURL);
    }

    public  function checkReplyChildExists($childReply, $bCheckChildren=false)
    {
        if ($bCheckChildren) return $this->findChild($childReply->sID);
        else
            foreach ($this->arrChildren as $child)
                if (($child!=null)&&($child->sID == $childReply->sID))
                    return true;

        return false;
    }

    public function cloneObject($source)
    {
        parent::cloneObject($source);
        $this->sTitle = $source->sTitle;
        $this->iNestedLevel = $source->iNestedLevel;
        $this->sMessageCode = $source->sMessageCode;

        $this->iNoSubReplies = $source->iNoSubReplies;
        $this->iNoUsersSubReplies = $source->iNoUsersSubReplies;
    }

}