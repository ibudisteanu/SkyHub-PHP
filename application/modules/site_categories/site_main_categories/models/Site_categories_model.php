<?php

require_once APPPATH.'modules/site_categories/site_category/models/Site_category_model.php';

class Site_categories_model extends Site_category_model
{
    public function __construct()
    {
        parent::__construct(true, false);
    }

    public function findTopCategories()
    {
        $cacheId = 'findTopCategories';
        return $this->loadContainerByFieldNameCached($cacheId, "Parent",null,array(),false,array("Importance"=>-1));
    }

    public function findCategories($sParentId, $iNumber=4)
    {
        $cacheId = 'findCategories_'.$sParentId;
        return $this->convertToArray($this->loadContainerByFieldNameCached($cacheId , "Parent",($sParentId != '' ? new MongoId($sParentId) : array('$exists'=>false)),array(),false,array("Importance"=>-1)));
    }

    /*
    OBSOLETE FUNCTION that doesn't use REGEX
    public function findAllCategories($sParentCategoryId, $iNumber=4)
    {
        $arraySiteCategories = [$sParentCategoryId];
        $arrResult = [];

        for ($index=0; $index < count($arraySiteCategories); $index++)
        {
            if (is_object($arraySiteCategories[$index])) $sCategoryId = $arraySiteCategories[$index]->sID;
            else if (is_string($arraySiteCategories[$index])) $sCategoryId = $arraySiteCategories[$index];
            else $sCategoryId = $arraySiteCategories[$index];

            $arrNewCategories = $this->findCategories($sCategoryId, 10);
            if ($arrNewCategories != null) {

                //dading these new categories
                foreach ($arrNewCategories as $newCategory)
                {
                    $bFound=false;
                    foreach ($arraySiteCategories  as $category)
                        if ($category == $newCategory)
                            $bFound=true;

                    if (!$bFound)
                    {
                        array_push($arrResult, $newCategory);
                        array_push($arraySiteCategories, $newCategory );
                    }
                }
            }
        }

        if ($sParentCategoryId != '')
            array_push($arrResult,$this->findCategory($sParentCategoryId,''));
        return $arrResult;
    }*/

    public function findAllCategories($sParentSiteCategoryId, $iNumber=4)
    {
        $sCacheId = 'findAllCategories_'.$sParentSiteCategoryId;

        $sRegex = "/,".$sParentSiteCategoryId.",/";

        if (($sParentSiteCategoryId == '') || ($sParentSiteCategoryId== null))
            $sRegex = '//';

        return $this->loadContainerByFieldNameCached($sCacheId,"SiteCatParents",array('$regex' => new MongoRegex($sRegex)));
    }

    public function findCategory($sId, $sCategoryFullURL='')
    {
        if ((!is_string($sId))&&(get_class($sId) == 'Site_category_model')) $sId = $sId->sID;
        if (($sId == '') && ($sCategoryFullURL!= ''))  $sId = $this->AdvancedCache->getIDFromFullURL($sCategoryFullURL);

        $cacheId = 'findCategory_'.$sId;
        return $this->loadContainerByIdOrFullURLCached($cacheId, $sId, $sCategoryFullURL);
    }

    public function findCategoryFromFullURL($sCategoryFullURL='')
    {
        return $this->loadContainerByIdOrFullURL('', $sCategoryFullURL);
    }

    public function resetCache()
    {
        parent::resetCache();
    }
}