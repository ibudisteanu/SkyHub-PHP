<?php

require_once APPPATH.'modules/forums/forum_categories/forum_category/models/Forum_category_model.php';

class Forum_categories_model extends Forum_category_model
{
    public $sClassName = 'Forum_category_model';

    public function __construct($bEnableChildren=true)
    {
        parent::__construct($bEnableChildren,null,null,false);

        $this->initDB('ForumCategories',TUserRole::notLogged, TUserRole::notLogged, TUserRole::Admin, TUserRole::SuperAdmin);
    }



    public function findForumCategoriesByForumId($sForumId='', $iNumber=4)
    {
        $sCacheId = 'findForumCategoriesByForumId_'.$sForumId;
        return $this->convertToArray($this->loadContainerByFieldNameCached($sCacheId, "ParentForumId",new MongoId($sForumId),array(),true,array("Importance"=>-1),false));//'Forum_categories_model',);
    }

    //using regex for materialize parents
    public function findForumCategoriesFromSiteCategoryId($sParentSiteCategoryId='', $iNumber=4)
    {
        $sCacheId = 'findForumCategoriesFromSiteCategoryId_'.$sParentSiteCategoryId;

        $sRegex = "/,".$sParentSiteCategoryId.",/";

        if (($sParentSiteCategoryId == '') || ($sParentSiteCategoryId == null))
            $sRegex = '//';

        return $this->convertToArray($this->loadContainerByFieldNameCached($sCacheId,"SiteCatParents",array('$regex' => new MongoRegex($sRegex))));
    }

    public function findForumCategoriesContainer($sId, $sCategoryFullURL='')
    {
        if (($sId == '') && ($sCategoryFullURL != ''))  $sId = $this->AdvancedCache->getIDFromFullURL($sCategoryFullURL);

        $sCacheId = 'findForumCategoriesContainer_'.$sId;
        return $this->loadContainerByIdOrFullURLCached($sCacheId, $sId, $sCategoryFullURL,[],true,'Forum_categories_model');
    }

    public function createIntroForumCategory($Forum)
    {
        $objForumCategoryIntro = new Form_category_model();
        $objForumCategoryIntro->sURLName = $Forum->sName;
        $objForumCategoryIntro->sParentForumId=$Forum->sID;
        $objForumCategoryIntro->storeUpdate($Forum);

        $Forum->sForumCategoriesId = $objForumCategoryIntro->sID;
        $Forum->storeUpdate();

        return $objForumCategoryIntro;
    }

    public function getForumCategory($sCategoryId, $sCategoryFullURL='')
    {
        if (($sCategoryId == '') && ($sCategoryFullURL!= ''))  $sCategoryId = $this->AdvancedCache->getIDFromFullURL($sCategoryFullURL);

        $sCacheId = 'getForumCategory_'.$sCategoryId;
        return $this->loadContainerByIdOrFullURLCached($sCacheId, $sCategoryId, $sCategoryFullURL);
    }

    public function findForumCategoryByFullURL($sFullURL='')
    {
        return $this->loadContainerByIdOrFullURL('', $sFullURL);
    }

    public function findAllForumCategories()
    {
        return $this->findAllCached('findAllForumCategories');
    }

    public function rewriteCache($bDeletion=false)
    {
        $this->AdvancedCache->rewriteCachedObject('findForumCategoriesContainer_'.$this->sID, $this);
        $this->AdvancedCache->rewriteCachedObject('findForumCategoriesByForumId_'.$this->sParentForumId, $this);

        $arrParents = explode(",",$this->sSiteCategoryParents);
        foreach ($arrParents as $sParent)
            if ($sParent != '')
            {
                $this->AdvancedCache->rewriteCachedObject('findForumCategoriesFromSiteCategoryId_'.$sParent, $this);
            }
    }

    public function resetCache()
    {
        parent::resetCache();

        //$this->AdvancedCache->delete('findForumCategories_'.$this->sID);
        //$this->AdvancedCache->delete('findForumCategoriesByForumId_'.$this->sID);
    }

}