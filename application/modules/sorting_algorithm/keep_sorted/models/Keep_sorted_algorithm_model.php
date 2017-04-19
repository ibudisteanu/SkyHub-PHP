<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'modules/sorting_algorithm/keep_sorted/models/Keep_sorted_data_model.php';

const SORTED_POPULAR_DATA_ELEMENTS_MAX = 200;
const SORTED_RECENT_DATA_ELEMENTS_MAX = 200;

class Keep_sorted_algorithm_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('order_algorithm/Order_coefficient_sort','OrderCoefficientSort');
    }

    public function getSortedData($sParentId='', $sSortedType='', $iMaxSortedElements=100)
    {
        $sortedData = new Keep_sorted_data_model($sSortedType, $iMaxSortedElements);

        if (!$sortedData->loadCompleteSortedData($sParentId))
            $sortedData->sParentId = $sParentId;

        return $sortedData;
    }

    public function getSortedDataMultiple($arrParents=[], $sSortedType='') //NOT WORKING
    {
        /*$this->initDB('SortedData'.$sSortedType,TUserRole::User,TUserRole::User,TUserRole::User,TUserRole::User);

        foreach ($arrParents)

        $mongosearch = [];*/
    }

    protected function recalculateMostSorted($sSortedType, $iMaxSortedElements, $Parents, $arrObjectData, $sFunctionName='recalculateElement')
    {
        $Parents .= ',,';
        $arrParents = explode(",",$Parents); //Rewriting all materialized parents

        $arrParentsVisitedAlready=[];

        foreach ($arrParents as $parent) {

            $bVisited=false;
            foreach ($arrParentsVisitedAlready as $visited)
                if ($visited == $parent){
                    $bVisited=true;
                    break;
                }

            array_push($arrParentsVisitedAlready, $parent);

            if (((strlen($parent) > 5 )||($parent == ''))&&(!$bVisited)) {
                $sortedData = $this->getSortedData($parent, $sSortedType, $iMaxSortedElements);

                if ($sFunctionName=='recalculateElement') $sortedData->recalculateElement($arrObjectData);
                elseif ($sFunctionName=='removeElement') $sortedData->removeElement($arrObjectData);
            }
        }
    }

    public function recalculateMostPopular($Parent, $object, $objectCoefficient, $sTypeObject=''){
        return $this->recalculateMostSorted('popular',SORTED_POPULAR_DATA_ELEMENTS_MAX,$Parent, ['sID'=>new MongoId($object->sID), 'c'=>$objectCoefficient,'t'=>$sTypeObject],'recalculateElement');
    }

    public function recalculateMostRecent($Parent, $object, $objectCoefficient, $sTypeObject=''){
        return $this->recalculateMostSorted('recent',SORTED_POPULAR_DATA_ELEMENTS_MAX,$Parent, ['sID'=>new MongoId($object->sID), 'c'=>$objectCoefficient,'t'=>$sTypeObject],'recalculateElement');
    }

    public function removeMostPopular($Parent, $object){
        return $this->recalculateMostSorted('popular',SORTED_POPULAR_DATA_ELEMENTS_MAX,$Parent, ['sID'=>new MongoId($object->sID), 'c'=>null,'t'=>''],'removeElement');
    }

    public function removeMostRecent($Parent, $object){
        return $this->recalculateMostSorted('recent',SORTED_POPULAR_DATA_ELEMENTS_MAX,$Parent, ['sID'=>new MongoId($object->sID), 'c'=>null,'t'=>''],'removeElement');
    }

    private function checkContentTypeEligibilityForDisplay($arrDisplayContentType, $sContentType){

        //return true; //just for debugging

        if (is_array($arrDisplayContentType))
            foreach ($arrDisplayContentType as $contentTypeEligible)
                if ($contentTypeEligible == $sContentType) return true;

        return false;
    }

    public function getSortedElementsHotness($Parent, &$arrIDsAlreadyUsed=[], $iPageIndex, $iNoPopular=20, $iNoPersonal=10, $arrDisplayContentType = ['topics'])
    {
        $sortedPopularData = $this->getSortedData($Parent,'popular',SORTED_POPULAR_DATA_ELEMENTS_MAX);
        $arrPopularElements = $sortedPopularData->getSortedElementsInRange(($iPageIndex-1)*$iNoPopular, $iNoPopular, $arrIDsAlreadyUsed);

        /*var_dump($arrPopularElements);
        var_dump($sortedPopularData);*/

        $result = [];
        for ($i=0; $i<count($arrPopularElements); $i++)
            if ($this->checkContentTypeEligibilityForDisplay($arrDisplayContentType, $arrPopularElements[$i]['t']))
                array_push($result, $arrPopularElements[$i]['sID'] );

        return $result;
    }

    public function getSortedElementsOld($Parent, &$arrIDsAlreadyUsed=[], $iPageIndex, $iNoPopular=20, $iNoRecent=5, $iNoPersonal=10, $arrDisplayContentType = ['topics'])
    {
        $sortedPopularData = $this->getSortedData($Parent,'popular',SORTED_POPULAR_DATA_ELEMENTS_MAX);
        $arrPopularElements = $sortedPopularData->getSortedElementsInRange(($iPageIndex-1)*$iNoPopular, $iNoPopular,$arrIDsAlreadyUsed);

        $sortedRecentData = $this->getSortedData($Parent,'recent',SORTED_RECENT_DATA_ELEMENTS_MAX);
        $arrRecentElements = $sortedRecentData->getSortedElementsInRange(($iPageIndex-1)*$iNoRecent, $iNoRecent,$arrIDsAlreadyUsed);

        $result = [];

        //$max = max(count($arrPopularElements),count($arrRecentElements));
        $max = count($arrPopularElements)+count($arrRecentElements);

        $counter1=0; $counter2=0; $c3=0;
        for ($i=0; $i<$max; $i++)
        {
            if ((count($arrPopularElements) > 0)&&(($i+1) % ($max / count($arrPopularElements)) == 0)&&($counter1 < count($arrPopularElements)) && ($this->checkContentTypeEligibilityForDisplay($arrDisplayContentType, $arrPopularElements[$counter1]['t'])))
            {
                array_push($result, $arrPopularElements[$counter1]['sID'] );
                //array_push($resultFullData, $arrPopularElements[$c1]);
                $counter1++;
            }

            if ((count($arrRecentElements) > 0)&&(($i+1) % ($max / count($arrRecentElements)) == 0)&&($counter2 < count($arrRecentElements))&& ($this->checkContentTypeEligibilityForDisplay($arrDisplayContentType, $arrRecentElements[$counter2]['t'])))
            {
                array_push($result, $arrRecentElements[$counter2]['sID'] );
                //array_push($resultFullData, $arrRecentElements[$c2]);
                $counter2++;
            }
        }

        return $result;
    }

    public function dropCollections(){

        $sortedPopularData = $this->getSortedData('','popular',SORTED_POPULAR_DATA_ELEMENTS_MAX);
        $sortedRecentData = $this->getSortedData('','recent',SORTED_RECENT_DATA_ELEMENTS_MAX);

        if (($sortedRecentData->dropCollection()) && ($sortedPopularData->dropCollection()))
            return true;
        else return false;
    }

}