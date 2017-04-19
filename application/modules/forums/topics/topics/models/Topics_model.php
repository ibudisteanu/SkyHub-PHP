<?php

require_once APPPATH.'modules/forums/topics/topic/models/Topic_model.php';

class Topics_model extends MY_Hierarchy_page_cached_model
{
    public $sClassName = 'Topic_model';

    public function __construct($bEnableChildren=true)
    {
        parent::__construct($bEnableChildren,null,null,true,true,false,true,true);

        $this->initDB('ForumTopics',TUserRole::notLogged, TUserRole::notLogged, TUserRole::User, TUserRole::SuperAdmin);
    }

    /*  Returning the top Topics from this parent this Parent*/
    public function findTopTopics($Parent, &$arrIDsAlreadyUsed, $iPageIndex=1, $iNoTopicsCount=20)
    {
        $iNoPopular=$iNoTopicsCount/2; $iNoRecent=($iNoTopicsCount-($iNoPopular))/3; $iNoPersonal=$iNoTopicsCount-($iNoPopular+$iNoRecent);

        $this->load->model('keep_sorted/Keep_sorted_algorithm_model','KeepSortedAlgorithm');
        $arrTopics = $this->KeepSortedAlgorithm->getSortedElements($Parent, $arrIDsAlreadyUsed, $iPageIndex, $iNoPopular, $iNoRecent, $iNoPersonal);

        return $this->findAllTopicsFromIDsList($arrTopics);
    }

    protected function findAllTopicsFromIDsList($arrIDsList)
    {
        if ((!is_array($arrIDsList)) || (count($arrIDsList) == 0)) return [];

        //$sCacheId = 'findAllTopicsFromIDsList_'.$sForumId;
        $arrResult = $this->convertToArray($this->loadContainerByFieldName("_id", ['$in' => $arrIDsList], array(), false));

        if ($arrResult == null) return [];

        $arrResultSorted = [];
        foreach ($arrIDsList as $id)
            foreach ($arrResult as $resultTopic)
                if ($resultTopic->sID == $id) {
                    array_push($arrResultSorted, $resultTopic);
                    break;
                }

        return $arrResultSorted;
    }

    //using regex for materialize parents
    public function findAllTopicsFromSiteCategoryMaterialized($sParentSiteCategoryId='', $iNumberOfTopics=400)
    {
        $sCacheId = 'findAllTopicsFromSiteCategoryMaterialized_'.$sParentSiteCategoryId;

        $sRegex = "/,".$sParentSiteCategoryId.",/";

        if (($sParentSiteCategoryId == '') || ($sParentSiteCategoryId == null))
            $sRegex = '//';

        return $this->loadContainerByFieldNameCached($sCacheId,"SiteCatParents",array('$regex' => new MongoRegex($sRegex)),[],null,null,null,$iNumberOfTopics);
    }

    public function addTopic()
    {
        $objNewTopic = new Topic_model();
        $objNewTopic->storeUpdate();
        return $objNewTopic;
    }

    public function addForumTopic($objParentForum)
    {
        $objNewTopic = new Topic_model();

        $objNewTopic->sParentForumId=$objParentForum->sID;
        $objNewTopic->storeUpdate();

        return $objNewTopic;
    }

    public function addForumCategoryTopic($objParentForumCategory)
    {
        $objNewTopic = new Topic_model();

        $objNewTopic->sParentForumId=$objParentForumCategory->sParentForumId;
        $objNewTopic->storeUpdate();

        return $objNewTopic;
    }

    public function findTopicByIdOrFullURL($sTopicId='', $sTopicFullURL='')
    {
        if (($sTopicId == '') && ($sTopicFullURL!= ''))  $sTopicId = $this->AdvancedCache->getIDFromFullURL($sTopicFullURL);

        $sCacheId = 'findTopicByIdOrFullURL_'.$sTopicId;
        return $this->loadContainerByIdOrFullURLCached($sCacheId, $sTopicId, $sTopicFullURL);
    }

    public function findTopicByFullURL($sTopicFullURL='')
    {
        return $this->loadContainerByIdOrFullURL('', $sTopicFullURL);
    }

    public function getTopic($sTopicId='', $sTopicFullURL='')
    {
        return $this->findTopicByIdOrFullURL($sTopicId, $sTopicFullURL);
    }

    public function findAllTopics()
    {
        return $this->findAll();
    }

    public function rewriteCache($bDeletion=false)
    {
        parent::rewriteCache($bDeletion);

        /*$this->AdvancedCache->rewriteCachedObject('findTopicsContainerByIdOrFullURL_'.$this->sID, $this, "","sID",$bDeletion);
        $this->AdvancedCache->rewriteCachedObject('findTopicContainersFromForum_'.$this->sParentForumId, $this, "", "sID", $bDeletion);*/
    }

    public function resetCache()
    {
        parent::resetCache();
        //$this->AdvancedCache->delete('findTopicsContainerByIdOrFullURL_'.$this->sID);
    }

}