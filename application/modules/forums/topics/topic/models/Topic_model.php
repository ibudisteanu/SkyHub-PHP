<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'core/models/MY_Hierarchy_page_cached_model.php';
require_once APPPATH.'modules/sorting_algorithm/order_algorithm/models/Order_coefficient.php';
require_once APPPATH.'modules/forums/replies/reply/models/Reply_model.php';

class Topic_model extends MY_Hierarchy_page_cached_model
{
    public $sClassName = 'Topic_model';
    public $sKeepSortedType = 'topic';

    public $sParentId;
    public $sParentForumId;
    public $sParentForumCategoryId;
    public $sParentSiteCategoryId;

    public $sSiteCategoryParents;//using Materialized Parents

    public $fImportance;

    public $sTitle;
    private $sBodyCode;
    private $sBodyCodeRendered;

    public $sShortDescription;

    public $arrAdditionalInfo;

    public function __construct($bEnableChildren=true, $hierarchyGrandParent = null, $hierarchyParent=null,
                                $bCreateVisitorsStatistics=true,$bCreateVoting=true, $bEnableMaterializedParents=true, $bEnableRepliesComponent=true, $bEnableImagesComponent=true)
    {
        //$this->FeaturesSubChildren = ["Name"=>"Topics","Array"=>"arrChildrenTopics"];
        parent::__construct($bEnableChildren, $hierarchyGrandParent, $hierarchyParent, $bCreateVisitorsStatistics, $bCreateVoting, $bEnableMaterializedParents, $bEnableRepliesComponent, $bEnableImagesComponent);

        if ($this->sTitle == '') $this->sTitle='NO TITLE';
        if ($this->sShortDescription == '') $this->sShortDescription ='NO DESCRIPTION';

        $this->initDB('ForumTopics',TUserRole::notLogged, TUserRole::notLogged, TUserRole::User, TUserRole::User);

        if ($this->MaterializedParentsModel != null)
        {
            $this->MaterializedParentsModel->clearMaterializedData();
            $this->MaterializedParentsModel->defineMaterializedData("Site_category_model","sSiteCategoryParents",$this,"sSiteCategoryParents");
        }

        $this->load->library('StringEmojisProcessing',null,'StringEmojisProcessing');
    }

    public function setBodyCode($sBodyCode)
    {
        if ($this->sBodyCode != $sBodyCode) {
            $this->sBodyCode = $sBodyCode;

            $this->StringEmojisProcessing->processForPlainTextEmojis($this->sBodyCode, true);
            $this->StringEmojisProcessing->removeEmojisImages($this->sBodyCode);

            $this->sBodyCodeRendered = '';
        }
    }

    public function getBodyCodeRendered()
    {
        if ($this->sBodyCodeRendered == '')
        {
            $this->sBodyCodeRendered = $this->sBodyCode;

            if ($this->StringEmojisProcessing != null)
                $this->StringEmojisProcessing->renderEmojisImages($this->sBodyCodeRendered);
        }

        return $this->sBodyCodeRendered;
        //return $this->sBodyCodeRendered;
    }

    public function getBodyCode()
    {
        return $this->sBodyCode;
    }

    public function getImagesFromBodyCode()
    {
        $this->objImagesComponent->getImagesFromBodyCode($this->sBodyCode);
    }
    protected function readCursor($p, $bEnableChildren=null)
    {
        parent::readCursor($p);

        if (isset($p['BodyCode'])) $this->sBodyCode = $p['BodyCode'];
        if (isset($p['Title'])) $this->sTitle = $p['Title'];

        if (isset($p['ParentForumId'])) $this->sParentForumId = (string)$p['ParentForumId'];
        else $this->sParentForumId = "";

        if (isset($p['ParentForumCategoryId'])) $this->sParentForumCategoryId = (string)$p['ParentForumCategoryId'];
        else $this->sParentForumCategoryId = "";

        if (isset($p['ParentSiteCategoryId'])) $this->sParentSiteCategoryId = (string)$p['ParentSiteCategoryId'];
        else $this->sParentSiteCategoryId = "";

        if (isset($p['SiteCatParents'])) $this->sSiteCategoryParents = (string) $p['SiteCatParents'];
        else $this->sSiteCategoryParents = '';

        //calculate the sParentId
        if (isset($p['ParentId'])) $this->sParentId = (string) $p['ParentId'];
        else
        {
            $this->sParentId  = '';
            if ($this->sParentForumId != '') $this->sParentId = $this->sParentForumId; else
                if ($this->sParentForumCategoryId != '') $this->sParentId = $this->sParentForumCategoryId; else
                    if ($this->sParentSiteCategoryId != '') $this->sParentId = $this->sParentSiteCategoryId;
        }

        if (isset($p['Importance'])) $this->fImportance = (float)$p['Importance'];
        else $this->fImportance = 1;

        if (isset($p['Image'])) $this->objImagesComponent->loadOldImage($p);

        if (isset($p['CoverImage'])) $this->objImagesComponent->loadOldCover($p);

        if (isset($p['ShortDescription'])) $this->sShortDescription = (string)$p['ShortDescription'];
        else $this->sShortDescription = '';

        if (isset($p['AddInfo'])) $this->arrAdditionalInfo = $p['AddInfo'];
    }

    protected function serializeProperties()
    {
        $arrResult = parent::serializeProperties();
        //var_dump($this);

        if ($this->sBodyCode != '')
        {
            $this->StringEmojisProcessing->removeEmojisImages($this->sBodyCode);
            $arrResult = array_merge($arrResult, array("BodyCode"=>$this->sBodyCode));
        }

        if ((isset($this->sTitle)) && ($this->sTitle!='NO TITLE')) $arrResult = array_merge($arrResult, array("Title"=>$this->sTitle));

        if (isset($this->sSiteCategoryParents))
            $arrResult = array_merge($arrResult, array("SiteCatParents"=>$this->sSiteCategoryParents));

        if ((isset($this->sParentForumId))&&($this->sParentForumId!='')) $arrResult = array_merge($arrResult, array("ParentForumId"=>new MongoId($this->sParentForumId)));

        if ((isset($this->sParentForumCategoryId))&&($this->sParentForumCategoryId!='')) $arrResult = array_merge($arrResult, array("ParentForumCategoryId"=>new MongoId($this->sParentForumCategoryId)));

        if ((isset($this->sParentSiteCategoryId))&&($this->sParentSiteCategoryId!='')) $arrResult = array_merge($arrResult, array("ParentSiteCategoryId"=>new MongoId($this->sParentSiteCategoryId)));

        if ((isset($this->sParentId))&&($this->sParentId!='')) $arrResult = array_merge($arrResult, array("ParentId"=>new MongoId($this->sParentId)));

        if ($this->fImportance != 0) $arrResult = array_merge($arrResult, array("Importance"=>$this->fImportance));

        if ((isset($this->sShortDescription)) && ($this->sShortDescription!='NO DESCRIPTION'))  $arrResult = array_merge($arrResult, array("ShortDescription"=>$this->sShortDescription));

        if (isset($this->arrAdditionalInfo)) $arrResult = array_merge($arrResult, array("AddInfo"=>$this->arrAdditionalInfo));

        $this->recalculateKeepSortedData();

        //var_dump($arrResult);
        return $arrResult;
    }

    public function calculateParents($objParent=null)
    {
        if ($objParent == null) return false;

        if (is_string($objParent))
        {
            $objParent = $this->AdvancedCache->getObjectFromId($objParent);
        }

        if ((isset($objParent))&&(is_object($objParent))&&(isset($objParent->sID)))
            $this->sParentId = $objParent->sID;

        if (get_class($objParent) === 'Forum_model') {
            $this->sParentForumId = $objParent->sID;
            $this->sParentSiteCategoryId = $objParent->sParentCategoryId;
        }

        if (get_class($objParent) === 'Forum_category_model') {
            $this->sParentForumCategoryId = $objParent->sID;
            $this->sParentForumId = $objParent->sParentForumId;

            $this->load->model('forum/Forums_model','ForumsModel');
            $forum = $this->ForumsModel->findForumById($objParent->sParentForumId);
            if ($forum != null)
            {

                $this->sParentSiteCategoryId = $forum ->sParentCategoryId;
            }
        }

        if (isset($objParent->sSiteCategoryParents))
            $this->sSiteCategoryParents  = $objParent->sSiteCategoryParents;

        if (get_class($objParent) == 'Site_category_model') {
            $this->sParentSiteCategoryId = $objParent->sID;
            $this->sSiteCategoryParents = $objParent->getSiteCategoryMaterializedParents();
        }

        /*if (($objParent != null)&&(isset($objParent->sSiteCategoryParents)))
            $this->sSiteCategoryParents = (method_exists($objParent,'getSiteCategoryMaterializedParents') ? $objParent->getSiteCategoryMaterializedParents() : $objParent->sSiteCategoryParents);*/

        return true;
    }

    protected function loadFromCursor($cursor, $bEnable=true)
    {
        parent::loadFromCursor($cursor, $bEnable);

        if (($this->sBodyCode != '')&&($this->sFullURLLink == '')&&(TUserRole::checkCompatibility($this->MyUser, TUserRole::Admin)))//Not URLFullName assigned
        {
            $this->sFullURLLink = $this->calculateFullURL();
            $this->updateContainerChild($this);
        }

        return true;
    }

    public function getTitleForIdName()
    {
        $this->load->library('StringsAdvanced',null,'StringsAdvanced');
        return $this->StringsAdvanced->getStringForIdName($this->sTitle);
    }


    public function getURL()
    {
        return base_url('topic/'.rtrim($this->sURLName,'/'));
    }

    public function getFullURL()
    {
        return base_url('topic/'.rtrim($this->sFullURLLink,'/'));
    }

    public function getUsedURL()
    {
        //return $this->getURL();
        return $this->getFullURL();
    }

    public function calculateFullURL()
    {
        if ($this->sParentForumCategoryId == '') return false;

        $this->load->model('forum_categories/forum_categories_model','ForumCategories');
        $Category = $this->ForumCategories->getForumCategory($this->sParentForumCategoryId);

        $sFullURL = rtrim($Category->sFullURLLink,'/').'/'.$this->sURLName;
        return $sFullURL;
    }

    /*  Used to sort the Topics from its parent category */
    private $objOrderCoefficient;
    public function calculateOrderCoefficient()
    {
        //if (isset($this->objOrderCoefficient)) return $this->objOrderCoefficient;
        $this->objOrderCoefficient = new Order_Coefficient();

        $iPersonalCoefficient = 0; $iPublicCoefficient = 0;

        //Personal Coefficient to a User
        if ($this->MyUser->UserActivities != null) //calculate personal interaction with the topic
        {
            $Activity = $this->MyUser->UserActivities->getFastTopicActivity($this->sID);
            $iPersonalCoefficient += $Activity->iActivityClicks*10 + $Activity->iActivityViews / 2;
            $iPersonalCoefficient += 3*$this->objVisitorsStatistics->getNumberViews() + $this->objVisitorsStatistics->getNumberSeen();

            //calculate personal interaction with the parent
            if ($this->sParentForumCategoryId != '')
            {
                $Activity = $this->MyUser->UserActivities->getFastForumActivity($this->sParentForumCategoryId);
                if ($Activity != null)
                    $iPersonalCoefficient += $Activity->iActivityClicks*2 + $Activity->iActivityViews / 2;
            }

            if ($this->sParentSiteCategoryId != '')
            {
                $Activity = $this->MyUser->UserActivities->getFastSiteCategoryActivity($this->sParentSiteCategoryId);
                if ($Activity != null)
                    $iPersonalCoefficient += $Activity->iActivityClicks*2 + $Activity->iActivityViews / 2;
            }
        }

        //show with a probability last topics I have also commented on

        $iPublicCoefficient +=  100*$this->objRepliesComponent->iNoReplies  + 200*$this->objRepliesComponent->iNoUsersReplies;
        $iPublicCoefficient += 10*count($this->objVote->arrVotes);
        //$iPublicCoefficient += 3*$this->objVisitorsStatistics->getNumberViews() + $this->objVisitorsStatistics->getNumberSeen();
        $iPublicCoefficient += $this->objVisitorsStatistics->getNumberViews()/3 + $this->objVisitorsStatistics->getNumberSeen()/6;

        $this->objOrderCoefficient->iPublicCoefficient  = $iPublicCoefficient;
        $this->objOrderCoefficient->iPersonalCoefficient = $iPersonalCoefficient;

        //$this->objOrderCoefficient->calculateHotnessCoefficient(($this->dtLastChangeDate != null ? $this->dtLastChangeDate : $this->dtCreationDate));
        $this->objOrderCoefficient->calculateHotnessCoefficient($this->dtCreationDate);

        return $this->objOrderCoefficient;
    }

    public function getKeepSortedDataParents()
    {
        return $this->sSiteCategoryParents.','.$this->sParentId.','.$this->sParentForumId.','.$this->sParentForumCategoryId;
    }

    public function rewriteCache($bDeletion=false)
    {
        parent::rewriteCache($bDeletion);

        $this->AdvancedCache->rewriteCachedObject('getTopic_'.$this->sID, $this, $bDeletion);
        $this->AdvancedCache->rewriteCachedObject('findTopicByIdOrFullURL_'.$this->sID, $this, $bDeletion);

        $this->AdvancedCache->rewriteCachedObject('find'.$this->sParentForumCategoryId, $this, '','sID',$bDeletion);

        $arrParents = explode(",",$this->sSiteCategoryParents); //Rewriting all materialized parents
        foreach ($arrParents as $sParent)
            if ($sParent != '')
            {
                $this->AdvancedCache->rewriteCachedObject('findForumsFromSiteCategory_'.$sParent, $this, $bDeletion);
                $this->AdvancedCache->rewriteCachedObject('findAllForumsFromSiteCategoryMaterialized_'.$sParent, $this, $bDeletion);
            }
    }

    public function resetCache()
    {
        parent::resetCache();

        /*$this->AdvancedCache->delete('getTopic_'.$this->sID);

        $this->AdvancedCache->delete('findTopicContainersFromForum_'.$this->sParentForumId);*/
    }


}