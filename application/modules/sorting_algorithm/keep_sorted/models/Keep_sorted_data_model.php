<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'core/models/MY_Simple_functions_model.php';

class Keep_sorted_data_model extends MY_Simple_functions_model
{
    public $arrSortedElements = [];
    public $sParentId = '';
    public $sSortedType='popular'; //it can be popular/recent

    private $iMaxSortedElements;

    public function __construct($sSortedType='popular', $iMaxSortedElements=100)
    {
        parent::__construct();

        $this->sID = (string) new MongoId();

        $this->sSortedType = $sSortedType;
        $this->iMaxSortedElements = $iMaxSortedElements;

        $this->initDB('SortedData'.ucfirst($sSortedType),TUserRole::User,TUserRole::User,TUserRole::User,TUserRole::User);
    }

    function sortedInsert($insert, $position)
    {
        if (is_int($position)) {
            array_splice($this->arrSortedElements, $position, 0, $insert);
        } else {
            $pos   = array_search($position, array_keys($this->arrSortedElements));
            $this->arrSortedElements = array_merge(
                array_slice($this->arrSortedElements, 0, $pos),
                $insert,
                array_slice($this->arrSortedElements, $pos)
            );
        }
    }

    private function insertNewElement($arrObjectData)
    {
        //5 4 1 0 and objectCoeff = 2 => 5 4 2 1 0

        for ($i=0; $i<count($this->arrSortedElements); $i++)
        {
            $sortedElement = $this->arrSortedElements[$i];

            if ($sortedElement['c'] < $arrObjectData['c'])
            {
                //insert in the list at the position i
                $this->sortedInsert([$arrObjectData],$i);
                //array_splice($this->arrSortedElements, $i-1, 0, $arrObjectData);

                if (count($this->arrSortedElements) > $this->iMaxSortedElements)
                    $this->arrSortedElements = array_slice($this->arrSortedElements, 0, $this->iMaxSortedElements);

                return true;
            }
        }

        if (count($this->arrSortedElements) < $this->iMaxSortedElements) {
            array_push($this->arrSortedElements, $arrObjectData);
            return true;
        }

        return false;
    }

    private function sortIncludingElement($arrObjectData)
    {
        $bChangeData=false;

        $iPos = $this->findElement($arrObjectData);
        if ($iPos != -1)
        {
            //The data will be sorted from grater to lower

            $i=$iPos;
            while (($i-1 >= 0)&&($this->arrSortedElements[$i-1]['c'] <  $arrObjectData['c']))
                $i--;

            while (($i+1 < count($this->arrSortedElements)&&($this->arrSortedElements[$i+1]['c'] > $arrObjectData['c'])))
                $i++;

            if ($i != $iPos)//We have a change
            {
                if (($iPos>=0)&&($iPos < count($this->arrSortedElements))) {
                    $this->myUnsetKeepingOrder($iPos);
                    //unset($this->arrSortedElements[$iPos]);
                    if ($i >= $iPos) $i--;
                }

                //insert in the list at the position i
                $this->sortedInsert([$arrObjectData],$i);
                //array_splice($this->arrSortedElements, $i-1, 0, $arrObjectData);

                $bChangeData=true;
            }

        } else
            $bChangeData = $this->insertNewElement($arrObjectData);

        if ($bChangeData)
            $this->updateCompleteSortedData();
    }

    public function recalculateElement($arrObjectData){
        $this->sortIncludingElement($arrObjectData);
    }

    public function removeElement($arrObjectData){
        foreach ($this->arrSortedElements as $key => $value)
        {
            if ((string)$value['sID'] == (string)$arrObjectData['sID']) {

                $this->myUnsetKeepingOrder($key);
                //unset ($this->arrSortedElements[$key]);
                $this->updateCompleteSortedData();

                return true;
            }
        }
        return false;
    }

    protected function myUnsetKeepingOrder($iPos) //Unset function removes the index making a gap in the array
    {
        //1 2 3 4 5
        //1 3 4 5
        for ($i=$iPos; $i< count($this->arrSortedElements)-1; $i++)
        {
            $this->arrSortedElements[$i] = $this->arrSortedElements[$i+1];
        }
        array_pop($this->arrSortedElements);
    }

    protected function findElement($arrObjectData)
    {
        foreach ($this->arrSortedElements as $key => $value){

            if ((string)$value['sID'] == (string)$arrObjectData['sID']) {
                return $key;
            }
        }

        return -1;
    }

    protected function readCursor($p, $bEnableChildren=null)
    {
        parent::readCursor($p, $bEnableChildren);

        if (isset($p['ParentId'])) $this->sParentId = (string) $p['ParentId'];
        if (isset($p["SortedElements"])) $this->arrSortedElements = $p['SortedElements'];
    }

    protected function serializeProperties()
    {
        $arrResult = [];

        $arrResult = array_merge($arrResult, array("ParentId"=>($this->sParentId == '' ? '' : new MongoId($this->sParentId) )));
        $arrResult = array_merge($arrResult, array("SortedElements"=>$this->arrSortedElements));

        return $arrResult;
    }

    public function getSortedElementsInRange($iStartingIndex, $iMaxCount, &$arrIDsAlreadyUsed=[])
    {
        if ($iStartingIndex < 0 ) $iStartingIndex = 0;
        else if ($iStartingIndex > 0) $iStartingIndex++;

        $result = []; $iCount = 0;
        for ($i=$iStartingIndex; $i < count($this->arrSortedElements); $i++)
        {
            $bFound=false;
            foreach ($arrIDsAlreadyUsed as $sIDAlready)
                if ((string)$this->arrSortedElements[$i]['sID'] == (string)$sIDAlready)
                {
                    $bFound=true;
                    break;
                }

            if (!$bFound)
            {
                array_push($result, $this->arrSortedElements[$i]);
                array_push($arrIDsAlreadyUsed, (string)$this->arrSortedElements[$i]['sID']);
                $iCount++;
            }
            if ($iCount >= $iMaxCount)
                break;
        }

        return $result;
    }

    public function loadCompleteSortedData($sParentId)
    {
        $sCacheId = 'getSortedData_'.$this->sSortedType.'_'.$sParentId.'';

        //if (!$result = $this->AdvancedCache->get($sCacheId )){
        if (1==1)
        {
            $result = $this->findOne(array("ParentId"=>($sParentId == '' ? $sParentId : new MongoId($sParentId))));
            if ($result != null) {
                $this->readCursor($result);
                return true;
                //$this->saveCacheData();
            }
        }

        return false;
    }

    protected function updateCompleteSortedData()
    {
        $MongoSearch = ["_id"=>new MongoId($this->sID)];
        $MongoData = $this->serializeProperties();

        $this->update($MongoSearch,$MongoData,'$set',true);
    }

    public function dropCollection(){
        return $this->collection->drop();
    }

}