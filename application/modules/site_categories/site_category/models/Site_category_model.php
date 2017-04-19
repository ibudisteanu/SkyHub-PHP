<?php

require_once APPPATH.'core/models/MY_Hierarchy_page_cached_model.php';
//require_once APPPATH.'modules/categories/category/models/Forum_sub_category_model.php';

class Site_category_model extends MY_Hierarchy_page_cached_model
{
    public $sClassName = 'Site_category_model';

    public $sName;

    public $sImage;
    public $sParentId;
    public $sSiteCategoryParents;//using Materialized Parents

    public $sCoverImage;

    public $sShortDescription;
    public $sDescription;

    public $iNoForums;
    public $iNoTopics;
    public $iNoComments;
    public $iNoUsers;

    public $bHideNameIconImage;

    public function __construct($bEnableChildren=true, $bEnableMaterializedParents=true)
    {
        parent::__construct($bEnableChildren,null,null,false,false,$bEnableMaterializedParents);
        $this->initDB('SiteCategories',TUserRole::notLogged, TUserRole::User, TUserRole::Admin, TUserRole::SuperAdmin);

        if ($this->MaterializedParentsModel != null)
        {
            $this->MaterializedParentsModel->clearMaterializedData();
            $this->MaterializedParentsModel->defineMaterializedData("Site_category_model","sSiteCategoryParents",$this,"sSiteCategoryParents");
        }

        if (!isset($this->sName)) $this->sName ='NO NAME';
        if (!isset($this->sImage)) $this->sImage='fa fa-file-excel-o';
    }

    //for updating the MaterializedParents for the future children
    public function getMaterializedDataInheritedForUpdate()
    {
        $data = [];

        //-----------------------------------------materialize for Site Categories--------------------------------------

        $this->load->model('site_main_categories/Site_categories_model','SiteCategoriesModel');
        $matObject = new MaterializedDataInherit("Site_category_model","sSiteCategoryParents",
                                                 $this->SiteCategoriesModel->findAllCategories($this->sID,10000), "sSiteCategoryParents");

        array_push($data, $matObject);

        //--------------------------------------------materialize for Forums--------------------------------------------

        $this->load->model('forum/Forums_model','ForumsModel');
        $matObject = new MaterializedDataInherit("Site_category_model","sSiteCategoryParents",
            $this->ForumsModel->findAllForumsFromSiteCategoryMaterialized($this->sID,10000), "sSiteCategoryParents");

        array_push($data, $matObject);

        //---------------------------------------materialize for Forum Categories---------------------------------------

        $this->load->model('forum_categories/Forum_categories_model','ForumCategoriesModel');

        $arrForums = $matObject->ProcessObject; $arrForumCategories=[];

        if ($arrForums != null)
            foreach ($arrForums as $Forum) {
                $arrCategories = $this->ForumCategoriesModel->findForumCategoriesByForumId($Forum->sID);
                if ($arrCategories != null)
                    foreach ($arrCategories as $category)
                        array_push($arrForumCategories, $category);
            }

        $matObject = new MaterializedDataInherit("Site_category_model","sSiteCategoryParents",  $arrForumCategories, "sSiteCategoryParents");

        array_push($data, $matObject);

        //------------------------------------------materialize for Topics----------------------------------------------

        $this->load->model('topics/Topics_model','TopicsModel');
        $matObject = new MaterializedDataInherit("Site_category_model","sSiteCategoryParents",
                $this->TopicsModel->findAllTopicsFromSiteCategoryMaterialized($this->sID,10000), "sSiteCategoryParents");

        array_push($data, $matObject);


        //---------------------------------------END OF MATERIALIZATION REFRESH-----------------------------------------


/*      $matObject = new MaterializedDataInherit("Forum_category_model","sSiteCategoryParents",
            $this->ForumCategoriesModel->findForumCategoriesFromSiteCategoryId($this->sID,10000), "sSiteCategoryParents");

        array_push($data, $matObject);*/

        return $data;
    }

    protected function readCursor($p, $bEnableChildren=null)
    {
        parent::readCursor($p, $bEnableChildren);
        $this->sName = $p['Name'];

        if (isset($p['Parent'])) $this->sParentId = (string)$p['Parent'];
        else $this->sParentId = "";

        if (isset($p['SiteCatParents'])) $this->sSiteCategoryParents = (string) $p['SiteCatParents'];
        else $this->sSiteCategoryParents = '';

        if (isset($p['HideNameIconImage'])) $this->bHideNameIconImage = (boolean) $p['HideNameIconImage'];
        else $this->bHideNameIconImage = false;

        if (isset($p['Image'])) $this->sImage = $p['Image'];
        if (isset($p['CoverImage'])) $this->sCoverImage = $p['CoverImage'];

        if (isset($p['Description']))  $this->sDescription = $p['Description'];
        if (isset($p['ShortDescription'])) $this->sShortDescription = $p['ShortDescription'];

        if (isset($p['NoForums'])) $this->iNoForums = $p['NoForums'];
        if (isset($p['NoTopics'])) $this->iNoTopics = $p['NoTopics'];
        if (isset($p['NoComments'])) $this->iNoComments = $p['NoComments'];
        if (isset($p['NoUsers'])) $this->iNoUsers = $p['NoUsers'];
    }

    protected function serializeProperties()
    {
        $arrResult = parent::serializeProperties();

        if (isset($this->sName)) $arrResult = array_merge($arrResult, array("Name"=>$this->sName));

        if (isset($this->sParentId)&&($this->sParentId != ''))
            if ($this->sParentId != $this->sID)
                $arrResult = array_merge($arrResult, array("Parent"=>new MongoId($this->sParentId)));

        if (isset($this->sSiteCategoryParents))
            $arrResult = array_merge($arrResult, array("SiteCatParents"=>$this->sSiteCategoryParents));

        if (isset($this->bHideNameIconImage)) $arrResult = array_merge($arrResult, array("HideNameIconImage"=>$this->bHideNameIconImage));

        if (isset($this->sImage)) $arrResult = array_merge($arrResult, array("Image"=>$this->sImage));
        if (isset($this->sCoverImage)) $arrResult = array_merge($arrResult, array("CoverImage"=>$this->sCoverImage));

        if (isset($this->sDescription)) $arrResult = array_merge($arrResult, array("Description"=>$this->sDescription));
        if (isset($this->sShortDescription)) $arrResult = array_merge($arrResult, array("ShortDescription"=>$this->sShortDescription));

        if ((isset($this->iNoForums)) && ($this->iNoForums != 0)) $arrResult = array_merge($arrResult, array("NoForums"=>$this->iNoForums));
        if ((isset($this->iNoTopics)) && ($this->iNoTopics != 0)) $arrResult = array_merge($arrResult, array("NoTopics"=>$this->iNoTopics));
        if ((isset($this->iNoComments) && ($this->iNoComments != 0))) $arrResult = array_merge($arrResult, array("NoComments"=>$this->iNoComments));
        if ((isset($this->iNoUsers) && ($this->iNoUsers != 0))) $arrResult = array_merge($arrResult, array("NoUsers"=>$this->iNoUsers));

        return $arrResult;
    }

    protected function loadFromCursor($cursor, $bEnable=true)
    {
        parent::loadFromCursor($cursor, $bEnable);

        if (($this->sFullURLLink == '')&&(TUserRole::checkCompatibility($this->MyUser, TUserRole::Admin)))//Not URLFullName assigned
            $this->calculateFullURL();

        return true;
    }

    public function calculateFullURL()
    {

    }

    public function getURL()
    {
        return base_url('category/'.rtrim($this->sURLName,'/'));
    }

    public function getFullURL()
    {
        return base_url('category/'.rtrim($this->sFullURLLink,'/'));
    }

    public function getUsedURL()
    {
        return $this->getFullURL();
    }

    public function getSiteCategoryMaterializedParents($bIncludeThis=true){
        return $this->sSiteCategoryParents.($this->sSiteCategoryParents != '' ? ',' : '').$this->sID;
    }

    public function findTopSubCategories($iNumber=4)
    {
        $this->load->model('site_main_categories/Site_categories_model','SiteCategoriesModel');
        return $this->SiteCategoriesModel->findCategories($this->sID,$iNumber);
    }


    public function resetCache()
    {
        parent::resetCache();

        $this->AdvancedCache->delete('findCategory_'.$this->sID);
        $this->AdvancedCache->delete('findCategories_'.$this->sID);
        $this->AdvancedCache->delete('findCategories_'.$this->sParentId);
        $this->AdvancedCache->delete('findTopCategories');
    }

}