<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'core/models/MY_Hierarchy_page_cached_model.php';
require_once APPPATH.'modules/forums/forum_categories/forum_category/models/Forum_category_model.php';
require_once APPPATH.'modules/forums/topics/topic/models/Topic_model.php';

class Forum_model extends MY_Hierarchy_page_cached_model
{
    public $sClassName = 'Forum_model';
    public $sKeepSortedType = 'forum';

    public $sName;

    public $sParentCategoryId;
    public $sSiteCategoryParents;//using Materialized Parents

    public $sForumCategoriesId;

    public $sImage;
    public $sCoverImage;
    public $sDescription;
    public $sDetailedDescription;

    public $iNoSubCategories;
    public $iNoTopics;
    public $iNoComments;
    public $iNoUsers;

    public $fImportance;

    public function __construct($bEnableChildren=true, $bEnableMaterializedParents=true)
    {
        parent::__construct($bEnableChildren,null,null,true,false,$bEnableMaterializedParents);
        //$this->load->model('forum_categories/forum_categories_model','ForumCategories');

        $this->initDB('Forums',TUserRole::notLogged, TUserRole::User, TUserRole::Admin, TUserRole::SuperAdmin);

        if ($this->MaterializedParentsModel != null)
        {
            $this->MaterializedParentsModel->clearMaterializedData();
            $this->MaterializedParentsModel->defineMaterializedData("Site_category_model","sSiteCategoryParents",$this,"sSiteCategoryParents");
        }

        if ($this->sName == null) $this->sName ='NO NAME';
        if ($this->sImage == null) $this->sImage='fa fa-file-excel-o';

        if (($this->arrCategories != null)&&(count($this->arrCategories) > 0))
            $this->callConstructorsCached($this->arrCategories);
    }

    protected function readCursor($p, $bEnableChildren=null)
    {
        parent::readCursor($p, $bEnableChildren=null);
        $this->sName = $p['Name'];
        if ($this->sURLName=='') $this->sURLName = $this->sName;

        if (isset($p['ParentCategory'])) $this->sParentCategoryId = (string)$p['ParentCategory'];

        if (isset($p['SiteCatParents'])) $this->sSiteCategoryParents = (string) $p['SiteCatParents'];
        else $this->sSiteCategoryParents = '';

        if (isset($p['Image'])) $this->sImage = $p['Image'];

        if (isset($p['ForumCategoriesId'])) $this->sForumCategoriesId = (string)$p['ForumCategoriesId'];

        if (isset($p['Importance'])) $this->fImportance = $p['Importance'];
        else $this->fImportance = 0 ;

        $this->sDescription = $p['Description'];
        $this->sDetailedDescription = $p['DetailedDescription'];

        if (isset($p['CoverImage'])) $this->sCoverImage = $p['CoverImage'];

        if (isset($p['NoSubCateg'])) $this->iNoSubCategories = $p['NoSubCateg'];
        if (isset($p['NoTopics'])) $this->iNoTopics = $p['NoTopics'];
        if (isset($p['NoComments'])) $this->iNoComments = $p['NoComments'];
        if (isset($p['NoUsers'])) $this->iNoUsers = $p['NoUsers'];

    }

    protected function serializeProperties()
    {
        $arrResult = parent::serializeProperties();

        if (isset($this->sName)) $arrResult = array_merge($arrResult, array("Name"=>$this->sName));

        if (isset($this->sParentCategoryId)) $arrResult = array_merge($arrResult, array("ParentCategory"=>new MongoId($this->sParentCategoryId)));

        if (isset($this->sSiteCategoryParents))
            $arrResult = array_merge($arrResult, array("SiteCatParents"=>$this->sSiteCategoryParents));

        if (isset($this->sForumCategoriesId)) $arrResult = array_merge($arrResult, array("ForumCategoriesId"=>new MongoId($this->sForumCategoriesId)));

        if (isset($this->fImportance)&&($this->fImportance != 0)) $arrResult = array_merge($arrResult, array("Importance"=>$this->fImportance));

        if (isset($this->sImage)) $arrResult = array_merge($arrResult, array("Image"=>$this->sImage));
        if (isset($this->sCoverImage)) $arrResult = array_merge($arrResult, array("CoverImage"=>$this->sCoverImage));

        if (isset($this->sDescription)) $arrResult = array_merge($arrResult, array("Description"=>$this->sDescription));
        if (isset($this->sDetailedDescription)) $arrResult = array_merge($arrResult, array("DetailedDescription"=>$this->sDetailedDescription));

        if (isset($this->sDescription)) $arrResult = array_merge($arrResult, array("Description"=>$this->sDescription));
        if ((isset($this->iNoSubCategories)) && ($this->iNoSubCategories != 0)) $arrResult = array_merge($arrResult, array("NoSubCateg"=>$this->iNoSubCategories));
        if ((isset($this->iNoForums)) && ($this->iNoForums != 0)) $arrResult = array_merge($arrResult, array("NoForums"=>$this->iNoForums));
        if ((isset($this->iNoTopics)) && ($this->iNoTopics != 0)) $arrResult = array_merge($arrResult, array("NoTopics"=>$this->iNoTopics));
        if ((isset($this->iNoComments) && ($this->iNoComments != 0))) $arrResult = array_merge($arrResult, array("NoComments"=>$this->iNoComments));
        if ((isset($this->iNoUsers) && ($this->iNoUsers != 0))) $arrResult = array_merge($arrResult, array("NoUsers"=>$this->iNoUsers));

        $this->recalculateKeepSortedData();

        return $arrResult;
    }

    protected function loadFromCursor($cursor, $bEnable=true)
    {
        parent::loadFromCursor($cursor, $bEnable);

        if (($this->sFullURLLink == '')&&(TUserRole::checkCompatibility($this->MyUser, TUserRole::Admin)))//Not URLFullName assigned
            $this->calculateFullURL();

        return true;
    }

    public function getURL()
    {
        return base_url('forum/'.rtrim($this->sURLName,'/'));
    }

    public function getFullURL()
    {
        return base_url('forum/'.rtrim($this->sFullURLLink,'/'));
    }

    public function getUsedURL()
    {
        return $this->getFullURL();
        //return $this->getURL();
    }

    public function calculateFullURL()
    {
        if ($this->sParentCategoryId == '') return false;

        $this->load->model('site_main_categories/Site_categories_model','SiteCategoriesModel');
        $Category = $this->SiteCategoriesModel->findCategory($this->sParentCategoryId);

        $sFullURL = rtrim($Category->sFullURLLink,'/').'/'.$this->sURLName;
        $sFullName = rtrim($Category->sFullURLName,'/').'/'.$this->sURLName;
        $sFullDomains = rtrim($Category->sFullURLDomains,'/').'/'.'forum';

        $this->update(array("_id"=>new MongoId($this->sID)) ,array("FullURLLink"=>$sFullURL, "FullURLDomains"=>$sFullDomains, "FullURLName"=>$sFullName));

        return $sFullURL;
    }

    public $arrCategories;
    public function getCategories()
    {
        if (!isset($this->arrCategories))
        {
            $this->load->model('forum_categories/forum_categories_model', 'ForumCategories');

            $this->arrCategories = $this->ForumCategories->findForumCategoriesByForumId($this->sID);
        }
        return $this->arrCategories;
    }

    /*  Used to sort the Forums */
    private $objOrderCoefficient;
    public function calculateOrderCoefficient()
    {
        //if (isset($this->objOrderCoefficient)) return $this->objOrderCoefficient;
        $this->objOrderCoefficient = new Order_coefficient();

        $iPersonalCoefficient = 0; $iPublicCoefficient = 0;

        if ($this->MyUser->UserActivities != null)
        {
            $Activity = $this->MyUser->UserActivities->getFastForumActivity($this->sID);
            $iPersonalCoefficient += $Activity->iActivityClicks*10 + $Activity->iActivityViews / 2;
        }


        $this->getCategories();
        foreach ($this->arrCategories as $Category)
            if ($Category != null)
            {
                $objTopicOrderCoefficient = $Category->calculateOrderCoefficient();

                $iPersonalCoefficient += $objTopicOrderCoefficient->iPersonalCoefficient;
                $iPublicCoefficient += $objTopicOrderCoefficient->iPublicCoefficient;
            }

        $iPublicCoefficient += count($this->arrCategories) / 2 + 3*$this->objVisitorsStatistics->getNumberViews() + $this->objVisitorsStatistics->getNumberSeen();
        if ((isset($this->fImportance)) && ($this->fImportance != 0))
            $iPublicCoefficient +=  (int) ( ($iPublicCoefficient + $this->fImportance) / 2);

        $this->objOrderCoefficient->iPublicCoefficient = $iPublicCoefficient;
        $this->objOrderCoefficient->iPersonalCoefficient = $iPersonalCoefficient;

        //$this->objOrderCoefficient->calculateHotnessCoefficient(($this->dtLastChangeDate != null ? $this->dtLastChangeDate : $this->dtCreationDate));
        $this->objOrderCoefficient->calculateHotnessCoefficient($this->dtCreationDate);

        return $this->objOrderCoefficient;
    }

    public function getKeepSortedDataParents()
    {
        return $this->sSiteCategoryParents.','.$this->sParentCategoryId;
    }

    public function callDestructorCached()
    {
        parent::callDestructorCached();
        MY_Advanced_model::$destructorCopy[$this->sID.'arrCategories'] = $this->arrCategories;
        unset($this->arrCategories);
    }

    public function retrieveBackDestructorCached()
    {
        parent::retrieveBackDestructorCached();
        $this->arrCategories = MY_Advanced_model::$destructorCopy[$this->sID.'arrCategories'];
        unset(MY_Advanced_model::$destructorCopy[$this->sID.'arrCategories'] );
    }

    public function rewriteCache($bDeletion=false)
    {
        parent::rewriteCache($bDeletion);
        $this->AdvancedCache->rewriteCachedObject('findForum_'.$this->sID, $this);

        $arrParents = explode(",",$this->sSiteCategoryParents); //Rewriting all materialized parents
        foreach ($arrParents as $sParent)
            if ($sParent != '')
            {
                $this->AdvancedCache->rewriteCachedObject('findForumsFromSiteCategory_'.$sParent, $this);
                $this->AdvancedCache->rewriteCachedObject('findAllForumsFromSiteCategoryMaterialized_'.$sParent, $this);
            }
        $this->AdvancedCache->rewriteCachedObject('findAllForums', $this);
    }

    public function resetCache()
    {
        parent::resetCache();

        /*$this->AdvancedCache->delete('findForumsFromSiteCategory_'.$this->sParentCategoryId);
        $this->AdvancedCache->delete('findAllTopForumsFromSiteCategory_'.$this->sParentCategoryId);
        $this->AdvancedCache->delete('findForum_'.$this->sID);
        $this->AdvancedCache->delete('findAllForums');*/
    }

}