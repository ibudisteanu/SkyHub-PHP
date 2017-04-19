<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'modules/forums/forums/forum/models/Forum_model.php';

class Forums_model extends Forum_model
{
    public $sClassName = 'Forum_model';
    public function __construct($bEnableChildren=true)
    {
        parent::__construct($bEnableChildren, false);
    }

    public function findForumById($sForumId='')
    {
        return $this->loadContainerById($sForumId);
    }

    public function findForumsFromSiteCategory($sSiteCategoryId='', $iNumber=4)
    {
        $cacheId = 'findForumsFromSiteCategory_'.$sSiteCategoryId;

        $result = $this->loadContainerByFieldNameCached($cacheId, "ParentCategory",new MongoId($sSiteCategoryId),array(),false,array("Importance"=>-1));
        return $this->convertToArray($result);
    }

    public function findTopForumsFromSiteCategory($sCategoryId='', $iPageIndex=1, $iNumber=4)
    {
        $arrForums = $this->findForumsFromSiteCategory($sCategoryId, $iPageIndex, $iNumber);
        return $this->sortForums($arrForums, $iPageIndex, $iNumber);
    }

    //using regex for materialize parents
    public function findAllForumsFromSiteCategoryMaterialized($sSiteCategoryId='', $iNumber=4)
    {
        $sCacheId = 'findAllForumsFromSiteCategoryMaterialized_'.$sSiteCategoryId;

        $sRegex = "/,".$sSiteCategoryId.",/";

        if (($sSiteCategoryId == '') || ($sSiteCategoryId== null))
            $sRegex = '//';

        return $this->convertToArray($this->loadContainerByFieldNameCached($sCacheId,"SiteCatParents",array('$regex' => new MongoRegex($sRegex))));
    }

    public function findForum($sId, $sForumFullURL='')
    {
        if (($sId == '') && ($sForumFullURL != ''))  $sId = $this->AdvancedCache->getIDFromFullURL($sForumFullURL);

        $sCacheId = 'findForum_'.$sId;
        return $this->loadContainerByIdOrFullURLCached($sCacheId, $sId, '');
    }

    public function findForumByFullURL($sForumFullURL='')
    {
        return $this->loadContainerByIdOrFullURL('', $sForumFullURL);
    }

    /* USED FOR API */
    public function findAllForums()
    {
        return $this->findAllCached('findAllForums');
    }


    /*  Sorting the forums from input */
    public function sortForums($arrForums, $iPageIndex=1, $iCount=10)
    {
        $this->load->model('order_algorithm/Order_coefficient_sort','OrderCoefficientSort');
        return $this->OrderCoefficientSort->sortCoefficientArray($arrForums, $iPageIndex, $iCount);
    }

    public function resetCache()
    {
        parent::resetCache();
    }

}