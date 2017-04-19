<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'core/models/MY_Hierarchy_page_model.php';

class MY_Hierarchy_page_cached_model extends MY_Hierarchy_page_model
{
    public  function  insertChild($child, $ChildrenDefinitionArrayName = null, $bInsertBeginning=false)
    {
        parent::insertChild($child, $ChildrenDefinitionArrayName, $bInsertBeginning);

        if (method_exists($child,'resetCache'))
            $child->resetCache();
    }
    public function loadChildByIdOrFullURLCached($sCacheId='', $sChildrenId, $sChildrenFullURL='', $sArrName='', $sQueryName='')
    {
        if (($sCacheId=='')||(!$content = $this->AdvancedCache->get($sCacheId)))
        {
            $content = $this->loadChildByIdOrFullURL($sChildrenId, $sChildrenFullURL, $sArrName, $sQueryName);

            if ($sCacheId != '')  $this->AdvancedCache->save($sCacheId, $content, 2678400);
        } else
            $this->callConstructorsCached($content);

        return $content;
    }

    public function loadContainerByIdOrFullURLCached($sCacheId='', $sID='', $sFullURL='', $fields=array(), $bChildren=null, $Sort=[],$bFindOne=true)
    {
        if (($sCacheId=='')||(!$content = $this->AdvancedCache->get($sCacheId)))
        {
            $content = $this->loadContainerByIdOrFullURL($sID, $sFullURL, $fields, $bChildren,$Sort,$bFindOne);

            if ($sCacheId != '')  $this->AdvancedCache->save($sCacheId, $content, 2678400);
        } else
            $this->callConstructorsCached($content);

        return $content;
    }

    public function findAllCached($sCacheId='', $fields=array(), $bChildren=null, $Sort=array(), $bFindOne=false)
    {
        if (($sCacheId=='')||(!$content = $this->AdvancedCache->get($sCacheId))) {
            $content = $this->loadContainerByQuery(array(), $fields, $bChildren, $Sort,$bFindOne);

            if ($sCacheId != '')  $this->AdvancedCache->save($sCacheId, $content, 2678400);
        } else
            $this->callConstructorsCached($content);

        return $content;
    }

    public function loadContainerByIdCached($sCacheId='',$sId, $fields=array(), $bChildren=null, $Sort=array(), $bFindOne=false)
    {
        if (($sCacheId=='')||(!$content = $this->AdvancedCache->get($sCacheId))) {
            $Query = array("_id" => ($sId != '' ? new MongoId($sId) : ''));
            $content = $this->loadContainerByQuery($Query, $fields, $bChildren, $Sort, $bFindOne);

            if ($sCacheId != '')  $this->AdvancedCache->save($sCacheId, $content, 2678400);
        } else
            $this->callConstructorsCached($content);

        return $content;
    }

    public function loadContainerByFieldNameCached($sCacheId='',$sFieldName, $sFieldValue, $fields=array(), $bChildren=null, $Sort=array(), $bFindOne=false, $iNumberOfResults=4000)
    {
        if (($sCacheId=='')||(!$content = $this->AdvancedCache->get($sCacheId))) {
            $Query = array ($sFieldName=>$sFieldValue);
            $content = $this->loadContainerByQuery($Query, $fields, $bChildren,$Sort, $bFindOne,$iNumberOfResults);

            if ($sCacheId != '')  $this->AdvancedCache->save($sCacheId, $content, 2678400);
        } else
            $this->callConstructorsCached($content);

        return $content;
    }

    public function loadContainerByAttachedIdCached($sCacheId='',$sAttachedId, $fields=array(), $bChildren=null, $Sort=array(), $bFindOne=true)
    {
        if (($sCacheId=='')||(!$content = $this->AdvancedCache->get($sCacheId))) {
            $Query = array ("AttachedParentId"=>new MongoId($sAttachedId));
            $content = $this->loadContainerByQuery($Query, $fields, $bChildren, $Sort, $bFindOne);

            if ($sCacheId != '')  $this->AdvancedCache->save($sCacheId, $content, 2678400);
        } else
            $this->callConstructorsCached($content);


        return $content;
    }

    public function resetCache()
    {

        if (($this->hierarchyParent != null) && ($this->hierarchyParent != $this))
        {
            if (method_exists($this->hierarchyParent,'resetCache'))
                $this->hierarchyParent->resetCache();
        }
    }

    public function rewriteCache($bDeletion=false)
    {
        if ($this->sInitialFullURLLink != $this->sFullURLLink)
            $this->AdvancedCache->delete('getIDFromFullURL_' . $this->sInitialFullURLLink);

        $this->AdvancedCache->rewriteCachedObject('getIDFromFullURL_' . $this->sInitialFullURLLink, $this->sID, $bDeletion);
        $this->AdvancedCache->rewriteCachedObject('getObjectFromId_'.$this->sID, $this, $bDeletion);

        //the reason: the type is the same...
        //$this->AdvancedCache->rewriteCachedObject('getObjectTypeFromId_'.$this->sID, $bDeletion);

        if (($this->hierarchyParent != null) && ($this->hierarchyParent != $this))
        {
            if (method_exists($this->hierarchyParent,'rewriteCache'))
                $this->hierarchyParent->rewriteCache(false);
        }
    }

    public function delete($sQueryFind = '_id', $sValue = '', $options=[])
    {
        $this->rewriteCache(true);
        $this->resetCache();
        return parent::delete($sQueryFind, $sValue);
    }

    /* resetting the cache */
    public function storeUpdate($parentObject='')
    {
        $bResult = parent::storeUpdate($parentObject);

        $this->rewriteCache(false);
        $this->resetCache();
        return $bResult;
    }

    /* resetting the cache */
    public function storeUpdateOnlyChild()
    {
        $bResult = parent::storeUpdateOnlyChild();

        $this->rewriteCache(false);
        $this->resetCache();
        return $bResult;
    }

    public function deleteContainerChild($Children, $sId='', $sChildrenName='')
    {
        $bResult = parent::deleteContainerChild($Children, $sId, $sChildrenName);

        //$this->rewriteCache(false);
        $this->resetCache();

        $Children->rewriteCache(true);
        $Children->resetCache();

        return $bResult;
    }

    public function insertContainerChild($Children, $sId='', $sChildrenName='')
    {
        $bResult = parent::insertContainerChild($Children, $sId, $sChildrenName);

        //$this->rewriteCache(false);
        $this->resetCache();

        $Children->rewriteCache(false);
        $Children->resetCache();
        return $bResult;
    }

    public function updateContainerChild($Children, $sId='', $MongoData=null, $sChildrenName='')
    {
        $bResult = parent::updateContainerChild($Children, $sId, $MongoData, $sChildrenName);
        //$this->rewriteCache(false);
        $this->resetCache();

        $Children->rewriteCache(false);
        $Children->resetCache();

        return $bResult;
    }

}