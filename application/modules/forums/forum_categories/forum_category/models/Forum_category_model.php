<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'core/models/MY_Hierarchy_page_cached_model.php';
require_once APPPATH.'modules/sorting_algorithm/order_algorithm/models/Order_coefficient.php';
require_once APPPATH.'modules/sorting_algorithm/order_algorithm/models/Order_coefficient_sort.php';

class Forum_category_model extends MY_Hierarchy_page_cached_model
{
    public $sClassName = 'Forum_category_model';
    public $sKeepSortedType = 'f_cat';

    public $sName;

    public $sParentForumId;
    public $sSiteCategoryParents;//using Materialized Parents

    public $sCoverImage;
    public $sImage;
    public $fImportance;
    public $sDescription;
    //public $iNoSubCategories;
    public $iNoTopics;
    public $iNoForums;
    public $iNoComments;
    public $iNoUsers;

    public function __construct($bEnableChildren=true, $hierarchyGrandParent = null, $hierarchyParent=null, $bCreateVisitorsStatistics=true)
    {
        parent::__construct($bEnableChildren, $hierarchyGrandParent, $hierarchyParent, $bCreateVisitorsStatistics);

        $this->initDB('ForumCategories',TUserRole::notLogged, TUserRole::notLogged, TUserRole::Admin, TUserRole::SuperAdmin);

        $this->load->library('StringsAdvanced',null,'StringsAdvanced');
        $this->load->model('topics/topics_model','TopicsModel');

        if ($this->sName == null) $this->sName ='NO NAME';
        if ($this->sImage == null) $this->sImage='fa fa-file-excel-o';
    }

    protected function readCursor($p, $bEnableChildren=null)
    {
        parent::readCursor($p, $bEnableChildren);

        if (isset($p['Name'])) $this->sName = $p['Name'];
        if ($this->sURLName=='') $this->sURLName = $this->sName;

        if (isset($p['ParentForumId'])) $this->sParentForumId = (string) $p['ParentForumId'];
        else
            if (isset($p['AttachedParentId'])) $this->sParentForumId = (string) $p['AttachedParentId'];

        if (isset($p['SiteCatParents'])) $this->sSiteCategoryParents = (string) $p['SiteCatParents'];
        else $this->sSiteCategoryParents = '';

        if (isset($p['Importance'])) $this->fImportance = (float)$p['Importance'];
        else $this->fImportance = 1;

        if (isset($p['Image'])) $this->sImage = $p['Image'];
        if (isset($p['CoverImage'])) $this->sCoverImage = $p['CoverImage'];
        if (isset($p['Description'])) $this->sDescription = $p['Description'];
        if (isset($p['NoSubcategories'])) $this->iNoForums = $p['NoSubcategories'];
        if (isset($p['NoForums'])) $this->iNoForums = $p['NoForums'];
        if (isset($p['NoTopics'])) $this->iNoTopics = $p['NoTopics'];
        //if (isset($p['Parent'])) $this->Parent = $p['Parent'];
        if (isset($p['NoComments'])) $this->iNoComments = $p['NoComments'];
        if (isset($p['NoUsers'])) $this->iNoUsers = $p['NoUsers'];
    }

    protected function serializeProperties()
    {
        $arrResult = parent::serializeProperties();

        if ((isset($this->sName)) && ($this->sName != 'NO NAME')) $arrResult = array_merge($arrResult, array("Name"=>$this->sName));
        if (isset($this->sParentForumId)) $arrResult = array_merge($arrResult, array("ParentForumId"=>new MongoId($this->sParentForumId)));

        if (isset($this->sSiteCategoryParents))
            $arrResult = array_merge($arrResult, array("SiteCatParents"=>$this->sSiteCategoryParents));

        if ($this->fImportance != 0) $arrResult = array_merge($arrResult, array("Importance"=>$this->fImportance));

        if ((isset($this->sImage)) && ($this->sImage != 'fa fa-file-excel-o')) $arrResult = array_merge($arrResult, array("Image"=>$this->sImage));
        if (isset($this->sCoverImage)) $arrResult = array_merge($arrResult, array("CoverImage"=>$this->sCoverImage));

        if (isset($this->sDescription)) $arrResult = array_merge($arrResult, array("Description"=>$this->sDescription));
        if ((isset($this->iNoForums)) && ($this->iNoForums != 0)) $arrResult = array_merge($arrResult, array("NoForums"=>$this->iNoForums));
        if ((isset($this->iNoTopics)) && ($this->iNoTopics != 0)) $arrResult = array_merge($arrResult, array("NoTopics"=>$this->iNoTopics));
        if ((isset($this->iNoComments) && ($this->iNoComments != 0))) $arrResult = array_merge($arrResult, array("NoComments"=>$this->iNoComments));
        if ((isset($this->iNoUsers) && ($this->iNoUsers != 0))) $arrResult = array_merge($arrResult, array("NoUsers"=>$this->iNoUsers));

        $this->recalculateKeepSortedData();

        return $arrResult;
    }


    public function getURL()
    {
        return base_url('forum/category/'.rtrim($this->sFullURLLink,'/'));
    }

    public function getFullURL()
    {
        return base_url('forum/category/'.rtrim($this->sFullURLLink,'/'));
    }

    public function getUsedURL()
    {
        return $this->getFullURL();
        //return $this->getURL();
    }

    /*  Used to sort the Forum Categories */
    private $objOrderCoefficient;
    public function calculateOrderCoefficient()
    {
        if (isset($this->objOrderCoefficient)) return $this->objOrderCoefficient;

        $this->objOrderCoefficient = new Order_coefficient();

        $iPersonalCoefficient = 0; $iPublicCoefficient = 0;

        if ($this->MyUser->UserActivities != null)
        {
            $Activity = $this->MyUser->UserActivities->getFastForumCategoryActivity($this->sID);
            $iPersonalCoefficient += $Activity->iActivityClicks*10 + $Activity->iActivityViews / 2;
        }

        /*$this->getTopics();

        if (isset($this->arrTopics))
        foreach ($this->arrTopics as $Topic)
            if ($Topic != null)
            {
                $objTopicOrderCoefficient = $Topic->calculateOrderCoefficient();
                $this->objOrderCoefficient->iPersonalCoefficient += $objTopicOrderCoefficient->iPersonalCoefficient;
                $this->objOrderCoefficient->iPublicCoefficient += $objTopicOrderCoefficient->iPublicCoefficient;
            }

            //var_dump($this); echo '<br/><br/><br/>';
        $iPublicCoefficient  += (isset($this->arrTopics) ? count($this->arrTopics) / 2 : 0) + 3*$this->objVisitorsStatistics->getNumberViews() + $this->objVisitorsStatistics->getNumberSeen();
        */

        $this->objOrderCoefficient->iPublicCoefficient += $iPublicCoefficient ;
        $this->objOrderCoefficient->iPersonalCoefficient += $iPersonalCoefficient;

        $this->objOrderCoefficient->calculateHotnessCoefficient($this->dtLastChangeDate);

        return $this->objOrderCoefficient;
    }

    public function getKeepSortedDataParents()
    {
        return $this->sParentForumId;
    }

    public function rewriteCache($bDeletion=false)
    {
        parent::rewriteCache($bDeletion);

        $this->AdvancedCache->rewriteCachedObject('getForumCategory_'.$this->sID, $this,$bDeletion);
        $this->AdvancedCache->rewriteCachedObject('findForumCategories_'.$this->sParentForumId, $this,$bDeletion);
        $this->AdvancedCache->rewriteCachedObject('findForumCategoriesByForumId_'.$this->sParentForumId, $this,$bDeletion);

        $arrParents = explode(",",$this->sSiteCategoryParents);
        foreach ($arrParents as $sParent)
            if ($sParent != '')
            {
                $this->AdvancedCache->rewriteCachedObject('findForumsFromSiteCategory_'.$sParent, $this, $bDeletion);
                $this->AdvancedCache->rewriteCachedObject('findAllForumsFromSiteCategoryMaterialized_'.$sParent, $this, $bDeletion);
            }

        $this->AdvancedCache->rewriteCachedObject('findAllForumCategories', $this);
    }

    public function resetCache()
    {
        parent::resetCache();

        //$this->AdvancedCache->delete('getForumCategory_'.$this->sID);
        //$this->AdvancedCache->delete('findForumCategoriesByForumId_'.$this->sParentForumId);

    }

}