<?php

require_once APPPATH.'modules/users/my_user/user/models/UserRoles.php';
require_once APPPATH.'core/models/MY_Advanced_model.php';
require_once APPPATH.'core/MongoConnection.php';

class MY_Hierarchy_model extends MY_Advanced_model
{
    public $sClassName = 'MY_Hierarchy_page_model';

    public $sAttachedParentId;
    public $hierarchyParent;
    public $hierarchyGrandParent;
    public $sHierarchyGrandParentId;
    public $bEnableChildren;

    //EXAMPLE public $arrChildrenDefinition = array(array("Name"=>"Children","Class"=>"MY_Hierarchy_model","Array"=>"arrChildren","EnableChildren"=>true,"CreateVisitorsStatistics"=>false));
    public $arrChildrenDefinition = [];

    public function __construct($bEnableChildren=false, $hierarchyGrandParent = null, $hierarchyParent=null, $bEnableMaterializedParents=false)
    {
        parent::__construct($bEnableMaterializedParents);

        $this->bEnableChildren =  $bEnableChildren;

        if ($this->hierarchyGrandParent == null) $this->hierarchyGrandParent=$hierarchyGrandParent;
        if ($this->hierarchyParent  == null) $this->hierarchyParent = $hierarchyParent;

        if (($this->hierarchyGrandParent == null) && ($this->hierarchyParent != null))
            ($this->hierarchyGrandParent = $this->hierarchyParent->hierarchyGrandParent);

        //var_dump($this->arrChildrenDefinition);

        foreach ($this->arrChildrenDefinition as $Definition)
            $this->childrenArrayInitialization($Definition["Array"]);
    }

    protected function childrenArrayInitialization($sArrayName)
    {
        if (!isset($this->{$sArrayName})) $this->{$sArrayName} = array();
        if (!isset($this->{$sArrayName.'Count'})) $this->{$sArrayName.'Count'} = 0;
    }

    public function findChild($sId, $bDeleteIt=false, $sFullURLLink='', $sURLName='')
    {
        $sId = (string) $sId;
        $result = null;
        if ($this->sID == $sId) return $this;

        if ((isset($this->sFullURLLink))&&($sFullURLLink!='')&&($this->sFullURLLink == $sFullURLLink ))  return $this;
        if ((isset($this->sURLName))&&($sURLName!='')&&($this->sURLName == $sURLName))  return $this;


        foreach ($this->arrChildrenDefinition as $Definition)
        {
            $sDefinitionArrayName = $Definition["Array"]; $iIndex=-1;
            foreach ($this->{$sDefinitionArrayName} as $Child)
            {
                $iIndex++;
                $result = $Child->findChild($sId,$bDeleteIt, $sFullURLLink, $sURLName);
                if ($result!=null)
                {
                    if (($bDeleteIt) && ($Child->sID == $sId))
                    {
                        if (method_exists($Child,'resetCache')) $Child->resetCache();
                        if (method_exists($Child,'rewriteCache')) $Child->rewriteCache(true);
                        unset($this->{$sDefinitionArrayName}[$iIndex]);
                    }
                    return $result;
                }
            }
        }
        return null;
    }

    public function deleteChild($sId='')
    {
        $this->findChild($sId,true);
        //var_dump($child);
        /*if ($child != null)
            unset($child);*/
    }

    public function insertChildHierarchy($child, $ChildrenDefinitionArrayName = null, $bInsertBeginning=false)
    {
        if ($ChildrenDefinitionArrayName== null)
            $ChildrenDefinitionArrayName = $this->arrChildrenDefinition[0]["Array"];

        if (!$bInsertBeginning)
            array_push($this->{$ChildrenDefinitionArrayName}, $child);
        else
            array_unshift($this->{$ChildrenDefinitionArrayName}, $child);

        $this->{$ChildrenDefinitionArrayName.'Count'} ++;

        //setting the hierarchies

        $child->hierarchyParent = $this;
        if ($this->hierarchyGrandParent == null)  $child->hierarchyGrandParent = $this;
        else $child->hierarchyGrandParent = $this->hierarchyGrandParent;

        $child->sAttachedParentId = $this->sID;

        $this->refreshSpecialParentHierarchy();
    }

    public function insertChild($child, $ChildrenDefinitionArrayName = null, $bInsertBeginning=false)
    {
        $this->insertChildHierarchy($child, $ChildrenDefinitionArrayName, $bInsertBeginning);
    }

    public function updateChild($child, $ChildrenDefinitionArrayName = null)
    {
        $this->deleteChild($child->sID);
        $this->insertChildHierarchy($child, $ChildrenDefinitionArrayName);
    }

    protected function refreshSpecialParentHierarchy()//it is used to refresh automatically Children special objects hierachy
    {
    }

    protected function readCursor($p, $bEnableChildren=null)
    {
        parent::readCursor($p, $bEnableChildren);

        if ($bEnableChildren == null) $bEnableChildren = $this->bEnableChildren;

        if (isset($p['AttachedParentId'])) $this->sAttachedParentId = (string) $p['AttachedParentId'];

        if (($bEnableChildren))
        {
            foreach ($this->arrChildrenDefinition as $Definition)
            {
                $FieldName = $Definition["Name"];
                $ClassName = $Definition["Class"];
                $ArrayName = $Definition["Array"];
                if ($Definition["EnableChildren"] == true) $bEnableReadChildren = $bEnableChildren;
                else $bEnableReadChildren = false;

                if (isset($Definition['CreateVisitorsStatistics'])) $bCreateVisitorsStatistics = (bool) $Definition['CreateVisitorsStatistics'];
                else $bCreateVisitorsStatistics  = false;

                if (isset($Definition['CreateVoting'])) $bCreateVoting =  (bool) $Definition['CreateVoting'];
                else $bCreateVoting = false;

                if (isset($Definition["EnableMaterializedParents"])) $bEnableMaterializedParents = (bool) $Definition['EnableMaterializedParents'];
                else $bEnableMaterializedParents = false;

                if (isset($Definition['EnableRepliesComponent'])) $bEnableRepliesComponent = (bool) $Definition['EnableRepliesComponent'];
                else $bEnableRepliesComponent = false;

                $this->{$ArrayName} = array();

                if (isset($p[$FieldName]))
                    $this->{$ArrayName} = $this->processChildren($ArrayName,$p[$FieldName],$ClassName,$bEnableReadChildren,
                        $bCreateVisitorsStatistics, $bCreateVoting, $bEnableMaterializedParents,$bEnableRepliesComponent);
            }
            $this->refreshSpecialParentHierarchy();
        }
    }

    protected function serializeProperties()
    {
        $arrResult = parent::serializeProperties();

        if ($this->sAttachedParentId != '')
            $arrResult = array_merge($arrResult,array("AttachedParentId"=>new MongoId($this->sAttachedParentId)));

        foreach ($this->arrChildrenDefinition as $Definition)
        {
            $FieldName = $Definition["Name"];
            $ClassName = $Definition["Class"];
            $ArrayName = $Definition["Array"];

            $arrDefinitionChildren = array();
            if (count($this->{$ArrayName}) >= 0)
            {
                foreach ($this->{$ArrayName} as $Child )
                {
                    array_push($arrDefinitionChildren,$Child->serializeProperties());
                }
                //var_dump($this->sID);
                //var_dump(count($arrDefinitionChildren));echo '<br/>';
                $arrResult = array_merge($arrResult,array($FieldName=>$arrDefinitionChildren));
            }
        }

        return $arrResult;
    }

    protected function serializeAndUpdateMongoData($bParent=false)
    {
        if (($bParent==false)&&($this->hierarchyGrandParent != $this )&&($this->hierarchyGrandParent!=null))
        {
            return $this->hierarchyGrandParent->serializeAndUpdateMongoData(true);
        }
        else
        {
            //var_dump($this->hierarchyGrandParent);
            $arrProperties = $this->serializeProperties();
            //var_dump($arrProperties);

            try {
                $this->updateById($this->sID, $arrProperties, "_id", true);
            } catch (Exception $exception){
                echo 'Error serializeAndUpdateMongoData() ...';
                var_dump($arrProperties);
                var_dump($exception->getMessage());
            }

            return $this->sID;
        }
    }


    public function processChildren($DynamicArrayName,$arraySource, $class='', $bEnableChildren=null,
                                    $bCreateVisitorsStatistics=false, $bCreateVoting=false, $bEnableMaterializedParents=false, $bEnableRepliesComponent)
    {
        if ($bEnableChildren == null) $bEnableChildren = $this->bEnableChildren;
        if ($class == '') $class= $this->sClassName;

        foreach ($arraySource as $item)
        {
            if (isset($item['_id']))
            {
                if ($class == 'string')
                {
                    $object = (string) $item;
                } else
                {
                    $object = new $class($bEnableChildren, $this->hierarchyGrandParent, $this,
                        $bCreateVisitorsStatistics, $bCreateVoting, $bEnableMaterializedParents, $bEnableRepliesComponent);

                    $this->insertChildHierarchy($object, $DynamicArrayName);

                    if ($this)
                        $object->loadFromCursor($item, $bEnableChildren);
                }

                $this->childrenArrayInsert($object, $DynamicArrayName);

                //array_push($this->{$$DynamicArrayName}, $object);
            }
        }
        //print_r($this->arrChildren);
        return $this->{$DynamicArrayName};
    }

    protected function childrenArrayInsert($object, $DynamicArrayName)
    {
        if (property_exists($object,$DynamicArrayName.'Count'))
            $this->{$DynamicArrayName.'Count'} += $object->{$DynamicArrayName.'Count'};
    }

    public function getParentId()
    {
        if ($this->hierarchyParent == null )
            return ''; //or $this->sAttachedParentId;
        else
            return $this->hierarchyParent->sID;
    }
    public function getGrandParentId()
    {
        if ($this->hierarchyGrandParent == null )
            return $this->sID;
        else
            return $this->hierarchyGrandParent->sID;
    }

    public function getAttachedGrandParentId()
    {
        if (isset($this->sHierarchyGrandParentId)&&($this->sHierarchyGrandParentId != '')) return $this->sHierarchyGrandParentId;

        if ($this->hierarchyGrandParent == null ) {

            if ($this->sAttachedParentId != '')
                return $this->sAttachedParentId;
            else
                return $this->sID;
        }
        else
            return $this->hierarchyGrandParent->sAttachedParentId;
    }

    public function loadChildByIdOrFullURL($sChildrenId, $sChildrenFullURL='', $sArrName='', $sQueryName='')
    {

        foreach ($this->arrChildrenDefinition as $Child)
            if (($sArrName == '') || (($sArrName == $Child["Name"]) ||($sArrName == $Child["Array"])))
            {
                if ($sQueryName == '')  $sQuery = $Child["Name"];
                else $sQuery = $sQueryName;

                if ($sChildrenId != '')
                {
                    $cursor = $this->findOne(array($sQuery."._id" => new MongoId($sChildrenId)), array());
                    if ($cursor != null) {
                        $object= new $this->sClassName(true);
                        $object->loadFromCursor($cursor);

                        $object = $object->findChild($sChildrenId,false);
                        //$object->loadFromCursor($cursor[$Child[0]][0]);
                        //var_dump($cursor['Children'][0]);
                        return $object;
                    }
                }

                if ($sChildrenFullURL != '')
                {

                    $cursor = $this->findOne(array($sQuery . ".FullURLLink" => $sChildrenFullURL), array());
                    if ($cursor != null) {
                        $object = new $this->sClassName(true);
                        $object->loadFromCursor($cursor);
                        $object = $object->findChild('xxxs',false, $sChildrenFullURL);
                        //$object->loadFromCursor($cursor[$Child[0]][0]);
                        //var_dump($cursor['Children'][0]);
                        return $object;
                    } else
                    {
                        $cursor = $this->findOne(array($sQuery . ".URLName" => $sChildrenFullURL), array());
                        if ($cursor != null) {
                            $object = new $this->sClassName(true);
                            $object->loadFromCursor($cursor);
                            $object = $object->findChild('xxxs',false, '', $sChildrenFullURL);
                            return $object;
                        }
                    }
                }

                if ($sQueryName != '')
                    return null;
            }
        return null;
    }

    public function deleteContainerChild($Children, $sId='', $sChildrenName='')
    {
        if (! TUserRole::checkCompatibility($this->MyUser, $this->roleRightsRemove))
        {
            $this->AlertsContainer->addAlert('g_msgGeneralError','error','Not enough rights for creating');
            return false;
        }

        if ((!is_string($Children)) && (is_object($Children)))  $sChildrenId = $Children->sID;
        else $sChildrenId = $Children;

        if (($sId == '')&&($this->sID != $sChildrenId)) $sId = $this->sID;
        if ($sChildrenName == '') $sChildrenName = $this->arrChildrenDefinition[0]["Name"];


        $arrSearch = array( );
        if ($sId != '')  $arrSearch = array_merge ($arrSearch,["_id"=>new MongoId($sId)]);
        $arrSearch = array_merge($arrSearch, [$sChildrenName."._id"=>new MongoId($sChildrenId)]);

        //$this->collection->update( $arrSearch, array('$unset' => array($sChildrenName.".$"=>"")));
        //$this->collection->update( $arrSearch , array('$pull' => [$sChildrenName.".$"=>["_id"=>new MongoId($sChildrenId)]]));
        //$this->collection->update( $arrSearch , array('$pull' => [$sChildrenName.".$._id"=>new MongoId($sChildrenId)]));
        $this->collection->update( $arrSearch , array('$pull' => [$sChildrenName=>["_id"=>new MongoId($sChildrenId)]]));
        return true;
    }

    public function updateContainerChild($Children, $sId='', $MongoData=null, $sChildrenName='')
    {
        if (! TUserRole::checkCompatibility($this->MyUser, $this->roleRightsUpdate))
        {
            $this->AlertsContainer->addAlert('g_msgGeneralError','error','Not enough rights for creating');
            return false;
        }

        if ((!is_string($Children)) && (is_object($Children)))
        {
            $sChildrenId = $Children->sID;
            $MongoData = $Children->serializeProperties();
        }
        else
        {
            $sChildrenId = $Children;
            if ((!is_array($MongoData)) && (is_object($MongoData))) $MongoData = $MongoData->serializeProperties();
        }

        if (($sId == '')&&($this->sID != $sChildrenId)) $sId = $this->sID;
        if ($sChildrenName == '') $sChildrenName = $this->arrChildrenDefinition[0]["Name"];

        $arrSearch = array( );
        if ($sId != '')  $arrSearch = array_merge ($arrSearch,["_id"=>new MongoId($sId)]);
        $arrSearch = array_merge($arrSearch, [$sChildrenName."._id"=>new MongoId($sChildrenId)]);

        $this->collection->update( $arrSearch, array('$set' => array($sChildrenName.".$"=>$MongoData)));
        return true;
    }

    public function insertContainerChild($Children, $sId='', $sChildrenName='')
    {
        if ((!is_array($Children)) && (is_object($Children))) $MongoData = $Children->serializeProperties();
        else $MongoData = $Children;

        if (! TUserRole::checkCompatibility($this->MyUser, $this->roleRightsInsert))
        {
            $this->AlertsContainer->addAlert('g_msgGeneralError','error','Not enough rights for creating');
            return false;
        }

        if ($sId == '') $sId = $this->sID;
        if ($sChildrenName == '') $sChildrenName = $this->arrChildrenDefinition[0]["Name"];

        $this->collection->update( array("_id"=>new MongoId($sId)), array( '$push' => array( $sChildrenName => $MongoData)));
        return true;
    }

    public function checkAuthorOccurrence($sAuthorId, $bExcludeGrandParent=false, $childExclusion=null)
    {
        if (($bExcludeGrandParent == false)&&($this->hierarchyGrandParent != null)&&($this != $this->hierarchyGrandParent))
            return $this->hierarchyGrandParent->checkAuthorOccurrence($sAuthorId, true, $childExclusion);

        if ($childExclusion == $this) return 0;

        if ($this->sAuthorId == $sAuthorId) return 1;

        $result = 0;
        foreach ($this->arrChildrenDefinition as $ChildrenDefinition)
        {
            $sDefinitionArrayName = $ChildrenDefinition["Array"];

            foreach ($this->{$sDefinitionArrayName} as $Child)
                $result += $Child->checkAuthorOccurrence($sAuthorId, true, $childExclusion);
        }
        
        return $result;
    }

    public function getAuthorsInvolved(&$arrAuthors, $bExcludeThisUser=true, $bExcludeGrandParent=false)
    {
        if (($bExcludeGrandParent == false)&&($this->hierarchyGrandParent != null)&&($this != $this->hierarchyGrandParent))
            return $this->hierarchyGrandParent->getAuthorsInvolved($arrAuthors, $bExcludeThisUser, true);

        if (!(($bExcludeThisUser) && ($this->sAuthorId == $this->MyUser->sID)))
        {
            $bFound=false;
            foreach ($arrAuthors as $author)
                if ($author == $this->sAuthorId)
                    $bFound=true;

            if (!$bFound)
                if ($this->sAuthorId != '')
                    array_push($arrAuthors, $this->sAuthorId);
        }

        foreach ($this->arrChildrenDefinition as $ChildrenDefinition)
        {
            $sDefinitionArrayName = $ChildrenDefinition["Array"];

            foreach ($this->{$sDefinitionArrayName} as $Child)
                $Child->getAuthorsInvolved($arrAuthors, $bExcludeGrandParent, true);
        }
    }

    public function removeParent($bSonAlready=false, $newGrandParent=null)
    {
        if (!$bSonAlready) $this->hierarchyParent = $newGrandParent;
        $this->hierarchyGrandParent = $newGrandParent;
        foreach ($this->arrChildrenDefinition as $ChildrenDefinition)
        {
            $sDefinitionArrayName = $ChildrenDefinition["Array"];

            foreach ($this->{$sDefinitionArrayName} as $Child) {
                $Child->removeParent(true, ($newGrandParent == null ? $this : $newGrandParent) );
            }
        }
    }

    public function cloneObject($object)
    {
        parent::cloneObject($object); // TODO: Change the autogenerated stub
        $this->sAttachedParentId = $object->sAttachedParentId;
        $this->sAuthorId = $object->sAuthorId;

    }

    public function callConstructorsCached($obj=null, $clearFoundArray=true)
    {
        if ($obj==null) $obj = $this;
        if ($clearFoundArray== true) $this->VariablesLibrary->arrCachedObjectsAlreadyVisited=[];

        if (!is_array($obj)) {
            foreach ($this->VariablesLibrary->arrCachedObjectsAlreadyVisited as $cachedObject)
                if ($cachedObject->sID == $obj->sID) return true;
            array_push($this->VariablesLibrary->arrCachedObjectsAlreadyVisited, $obj);
        }

        if (is_array($obj))
        {
            foreach ($obj as $element)
                $this->callConstructorsCached($element, false);

            return;
        }

        $obj->__construct($obj->bEnableChildren, $obj->hierarchyGrandParent, $obj->hierarchyParent,
            (isset($obj->bCreateVisitorsStatistics) ? $obj->bCreateVisitorsStatistics : false),
            (isset($obj->bCreateVoting) ? $obj->bCreateVoting : false));

        foreach ($obj->arrChildrenDefinition as $ChildrenDefinition)
        {
            $sDefinitionArrayName = $ChildrenDefinition["Array"];
            foreach ($obj->{$sDefinitionArrayName} as $Child)
                $this->callConstructorsCached($Child, false);
        }

        if ($obj->hierarchyParent != null)
            $this->callConstructorsCached($obj->hierarchyParent, false);
    }

}
