<?php

require_once APPPATH.'core/models/components/Component_Materialized_parents_model.php';

class MY_Advanced_model extends CI_Model
{
    public $sClassName = 'MY_Advanced_model';
    public $sKeepSortedType = 'default';

    public $sID;

    public $sAuthorId='';//who created this
    public $MaterializedParentsModel;

    protected $sTable;
    protected $collection;

    protected $roleRightsRead=TUserRole::SuperAdmin;
    protected $roleRightsUpdate=TUserRole::SuperAdmin;
    protected $roleRightsInsert=TUserRole::SuperAdmin;
    protected $roleRightsRemove=TUserRole::SuperAdmin;

    public $dtCreationDate;
    public $dtLastChangeDate;
    public $sLastChangeUserId;
    public $bLastChanged=false; //used to store the change automatically

    public function __construct($bEnableMaterializedParents=false)
    {
        parent::__construct();

        $this->load->library('VariablesLibrary',null,'VariablesLibrary');

        if ($bEnableMaterializedParents)
            $this->MaterializedParentsModel = new MaterializedParentsModel($this);

        if ($this->sID == null) $this->sID = new MongoId();
    }

    // fake "extends MaterializedParentsModel" using magic function
    public function __call($method, $args)
    {
        if ($this->MaterializedParentsModel != null)
            $this->MaterializedParentsModel ->$method($args[0]);
    }

    public function initDB($sTable, $rightsRead, $rightsUpdate, $rightsInsert, $rightsRemove)
    {
        $this->sTable = $sTable;

        $this->collection = MongoConnection::selectCollection($this->sTable);

        $this->roleRightsRead = $rightsRead;
        $this->roleRightsUpdate = $rightsUpdate;
        $this->roleRightsInsert = $rightsInsert;
        $this->roleRightsRemove = $rightsRemove;
    }

    public function loadContainerByQuery($Query, $fields=array(), $bEnableChildren=null, $Sort=array(), $bFindOne=false, $iNumberOfResults=2000)
    {
        if (($bEnableChildren == null) &&(isset($this->bEnableChildren))) $bEnableChildren = $this->bEnableChildren;

        if (! TUserRole::checkCompatibility($this->MyUser, $this->roleRightsRead))
        {
            $this->AlertsContainer->addAlert('g_msgGeneralError','error','Not enough rights for read');
            return false;
        }

        //$bFindOne=false;
        if ($bFindOne)
        {
            if ($Sort != []) $cursor = $this->collection->findOne($Query, $fields)->sort($Sort);
            else $cursor = $this->collection->findOne($Query, $fields);

            //var_dump($Query);
            return $this->processElement($cursor,'', $bEnableChildren);
        } else
        {
            if ($Sort != []) $cursor = $this->collection->find($Query, $fields)->sort($Sort)->limit($iNumberOfResults);
            else $cursor = $this->collection->find($Query, $fields);

            //var_dump($cursor);

            $count = $cursor->count();
            if ($count > 0)
                //$this->processArray($cursor,$class);
                return $this->processArray($cursor,'',$bEnableChildren);
            else
                return null;

        }
    }


    protected function processObject($item, $class, $bEnableChildren)
    {
        if ($item == null) return null;

        $itemObject=null;
        if ($class == 'string')
        {
            $itemObject = (string) $item;
        } else
        {
            $itemObject = new $class();
            $itemObject->loadFromCursor($item,$bEnableChildren);
        }
        return $itemObject;
    }

    public function processElement($item, $class, $bEnableChildren)
    {
        if ($class == '') $class= $this->sClassName;
        if (($bEnableChildren == null) &&(isset($this->bEnableChildren))) $bEnableChildren = $this->bEnableChildren;

        return $this->processObject($item, $class, $bEnableChildren);
    }

    public function processArray($arraySource, $class='', $bEnableChildren=null)
    {
        if ($class == '') $class= $this->sClassName;
        if (($bEnableChildren == null) &&(isset($this->bEnableChildren))) $bEnableChildren = $this->bEnableChildren;

        $array = array();

        if (is_array($arraySource) || (get_class($arraySource) == 'MongoCursor'))
        {
            foreach ($arraySource as $item) {
                $itemObject = $this->processObject($item, $class, $bEnableChildren);

                if ($itemObject != null)  array_push($array, $itemObject);
            }
        }

        if (count($array)==1) return $array[0];
        else return $array;
    }

    public function findAll($fields=array(), $bChildren=null, $Sort=array())
    {
        return $this->loadContainerByQuery(array(),$fields,$bChildren,$Sort,false);
    }

    public function loadContainerById($Id, $fields=array(), $bChildren=null, $Sort=array(), $bFindOne=false)
    {
        if ((!is_string($Id))&&(is_object($Id))&&(get_class($Id) != 'MongoId')) $Id = $Id->sID;

        $Query = array ("_id"=>($Id != '' ? new MongoId($Id) : ''));
        return $this->loadContainerByQuery($Query, $fields, $bChildren,$Sort,$bFindOne);
    }

    public function loadContainerByFieldName($sFieldName, $sFieldValue, $fields=array(), $bChildren=null, $Sort=array(), $bFindOne=false)
    {
        $Query = array ($sFieldName=>$sFieldValue);
        return $this->loadContainerByQuery($Query, $fields, $bChildren,$Sort, $bFindOne);
    }

    public function loadContainerByAttachedId($sAttachedId, $fields=array(), $bChildren=null, $Sort=array(), $bFindOne=true)
    {
        $Query = array ("AttachedParentId"=>new MongoId($sAttachedId));
        return $this->loadContainerByQuery($Query, $fields, $bChildren, $Sort, $bFindOne);
    }

    public function loadContainerByIdOrFullURL($sID='', $sFullURL='', $fields=array(), $bChildren=null, $Sort=[], $bFindOne=true)
    {
        if ($sID != '')
            return $this->loadContainerById(new MongoId($sID),$fields,$bChildren);
        else
            if ($sFullURL != '') {
                return $this->loadContainerByFieldName('FullURLLink', $sFullURL, $fields, $bChildren,$Sort,$bFindOne);
            } else
                if ($sID == '')
                    return $this->loadContainerById('',$fields,$bChildren,$Sort, $bFindOne);

        return null;
    }

    protected function readCursor($p, $bEnableChildren=null)
    {
        $this->sID = (string)reset($p);
        //var_dump($p);

        if (isset($p['CreationDate'])) $this->dtCreationDate = $p['CreationDate'];
        if (isset($p['LastChangeDate'])) $this->dtLastChangeDate = $p['LastChangeDate'];
        if (isset($p['LastChangeUserId'])) $this->sLastChangeUserId = $p['LastChangeUserId'];

        if (isset($p['AuthorId'])) $this->sAuthorId = (string) $p['AuthorId'];
    }

    protected function serializeProperties()
    {
        $arrResult = array();

        if (($this->sID == '')||(strlen($this->sID) < 10))
            $this->sID = (string) new MongoId();

        $arrResult = array_merge($arrResult, array("_id" => new MongoId($this->sID)));

        $bJustCreated=false;
        if (!isset($this->dtCreationDate))
        {
            $this->dtCreationDate = $this->TimeLibrary->getDateTimeNowUnixMongoDate();
            $bJustCreated=true;
        }

        if (isset($this->dtCreationDate))
            $arrResult = array_merge($arrResult, array("CreationDate"=>$this->dtCreationDate));

        if ((!$bJustCreated)&&($this->bLastChanged)) {
            $this->dtLastChangeDate = $this->TimeLibrary->getDateTimeNowUnixMongoDate();
            $this->sLastChangeUserId = $this->MyUser->sID;
        }

        if ((isset($this->dtLastChangeDate)))
            $arrResult = array_merge($arrResult, array("LastChangeDate"=>$this->dtLastChangeDate));
        if (isset($this->sLastChangeUserId))
            $arrResult = array_merge($arrResult, array("LastChangeUserId"=>new MongoId($this->sLastChangeUserId)));

        if ($this->sAuthorId != '') $arrResult = array_merge($arrResult, array("AuthorId"=>new MongoId($this->sAuthorId)));

        return $arrResult;
    }

    public function getAuthorName()
    {
        $this->load->model('users/Users_minimal','UsersModel');
        $User = $this->UsersModel->userByMongoId($this->sAuthorId);
        if ($User != null)
            return $User->getFullName();
        else
            return '';
    }

    protected function serializeAndUpdateMongoData()
    {
        //var_dump($this->hierarchyGrandParent);
        $arrProperties = $this->serializeProperties();
        //var_dump($arrProperties);
        $this->updateById($this->sID, $arrProperties,"_id",true);

        return $this->sID;
    }

    public function storeUpdate($parentObject='')
    {
        if ($this->MaterializedParentsModel != null) {

            $this->MaterializedParentsModel->refreshChildren(true,  true, false, false,     true, false);//remove

            if (($parentObject!='')&&(is_object($parentObject))) {
                $this->MaterializedParentsModel->clearMaterializedParent();
                $this->MaterializedParentsModel->addMaterializedParent($parentObject);
            }

            $this->MaterializedParentsModel->refreshChildren(false, false, true, true,     false, true);//add
        }

        return $this->serializeAndUpdateMongoData();
    }

    public function storeUpdateOnlyChild()
    {
        if ((isset($this->hierarchyParent))&&($this->hierarchyParent != null)) $this->hierarchyParent->updateContainerChild($this);
        else $this->storeUpdate();
    }

    public function getMaterializedDataInheritedForUpdate()
    {
        return [];
    }

    protected function loadFromCursor($cursor,$bEnableChildren=null)
    {

        //var_dump($cursor);
        //var_dump(iterator_to_array($cursor));

        if (((is_object($cursor))&&(get_class($cursor)=='MongoCursor'))) {
                $cursor = iterator_to_array($cursor);
                foreach ($cursor as $p) {
                    if ($p != null)
                        $this->readCursor($p, $bEnableChildren);
                }
        } else
        if ($cursor != null)
            $this->readCursor($cursor, $bEnableChildren);
    }

    public function convertToArray($object)
    {
        if ($object == null) return [];

        if (is_array($object)) return $object;
        else return (array($object));
    }

    public function getCreationDate()
    {
        if ($this->dtCreationDate == null)
        {
            $date = $this->TimeLibrary->getDateTimeNowUnixMongoDate();
            return $date;
        }
        else return $this->dtCreationDate;
    }

    public function getCreationDateString($sFormat='Y-M-d H:i')
    {
        return date($sFormat, ($this->getCreationDate()->sec));
    }

    public function getLastChangeDateExistence()
    {
        if ($this->dtLastChangeDate != null) return true;
        else return false;
    }

    public function getLastChangeDate()
    {
        return $this->dtLastChangeDate;
        /*
        if ($this->dtLastChangeDate == null)
        {
            $date = $this->TimeLibrary->getDateTimeNowUnixMongoDate();
            return $date;
        }
        else $this->dtLastChangeDate;
        */
    }

    public function getLastChangeDateString($sFormat='Y-M-d H:i')
    {
        $date = $this->getLastChangeDate();
        if ($date != null)
            return date($sFormat, ($date->sec));
        else
            return '';
    }

    public function checkOwnership($sUserToCheckId = '')
    {
        if ($sUserToCheckId == '') $sUserToCheckId = $this->MyUser->sID;

        //echo $UserId;
        if ((($sUserToCheckId!='')&&($this->sAuthorId == $sUserToCheckId))
            ||(($sUserToCheckId == $this->MyUser->sID)&&(TUserRole::checkUserRights(TUserRole::Admin))))
            return true;
        else
            return false;
    }

    public function delete($sQueryFind='_id',$sValue='',$options=[])
    {
        if (! TUserRole::checkCompatibility($this->MyUser, $this->roleRightsRemove))
        {
            $this->AlertsContainer->addAlert('g_msgGeneralError','error','Not enough rights for deleting the object');
            return ['result'=>false,'message'=>'Not enough rights to delete the object'];
        }

        if (!(( TUserRole::checkUserRights(TUserRole::Admin)) || ($this->sAuthorId == $this->MyUser->sID))) {
            $this->AlertsContainer->addAlert('g_msgGeneralError','error','Not enough rights for deleting the object');
            return ['result'=>false,'message'=>'Not enough rights for deleting the object'];
        }

        if ($this->MaterializedParentsModel != null) {
            $this->MaterializedParentsModel->refreshChildren(false, true, false, false,     false, true);//remove

            if (($sQueryFind != '') && (!is_string($sQueryFind)) && (is_object($sQueryFind))) {

                $parentObject = $sQueryFind;
                $sQueryFind = '_id';
            }
        }

        if ($sQueryFind=='_id')
        {
            if ($sValue == '') $sValue = $this->sID;
            $sValue = new MongoId($sValue);

            $options = array('justOne' => true);
        }

        $arrQuery = array ($sQueryFind=>$sValue);
        $cursor = $this->collection->remove($arrQuery, $options);

        $count = $cursor['n'];

        if ($count > 0)
        {
            $this->removeObjectFromKeepSortedData();
            return ['result'=>true,'message'=>"The delete has been successfully"];
        }
        return ['result'=>false,'message'=>"The delete couldn't be performed, because no object found"];
    }

    public function updateByQuery($MongoQuery, $MongoData, $bUpset=false)
    {
        if (! TUserRole::checkCompatibility($this->MyUser, $this->roleRightsUpdate))
        {
            $this->AlertsContainer->addAlert('g_msgGeneralError','error','Not enough rights for updating object');
            return false;
        }

        if ($bUpset)
        {
            $this->collection->update($MongoQuery,array('$set'=>$MongoData),array("upsert"=>$bUpset));
        } else
            $this->collection->update($MongoQuery,array('$set'=>$MongoData));
        return true;
    }

    public function updateById($sID, $MongoData, $sQueryFind="_id", $bUpset=false)
    {
        if (! TUserRole::checkCompatibility($this->MyUser, $this->roleRightsUpdate))
        {
            $this->AlertsContainer->addAlert('g_msgGeneralError','error','Not enough rights for updating object');
            return false;
        }

        if ($bUpset)
        {
            $this->collection->update(array ($sQueryFind=>new MongoId($sID)),array('$set'=>$MongoData),array("upsert"=>$bUpset));
        } else
            $this->collection->update(array ($sQueryFind=>new MongoId($sID)),array('$set'=>$MongoData));
        return true;
    }

    public function find($query=array(), $fields=array())
    {
        if (! TUserRole::checkCompatibility($this->MyUser, $this->roleRightsRead))
        {
            $this->AlertsContainer->addAlert('g_msgGeneralError','error','Not enough rights for the procedure');
            return false;
        }

        return $this->collection->find($query, $fields);
    }

    public function findOne($query=array(), $fields=array())
    {
        return $this->collection->findOne($query, $fields);
    }

    public function dataCount()
    {
        $array = $this->convertToArray($this->findAll(array(),true));
        return count($array);
    }

    public function getKeepSortedDataParents()
    {
        return '';
    }

    public function recalculateKeepSortedData()
    {
        $sParents = $this->getKeepSortedDataParents();
        if ($sParents == null) return;

        $this->load->model('keep_sorted/keep_sorted_algorithm_model','KeepSortedAlgorithmModel');

        $this->KeepSortedAlgorithmModel->recalculateMostPopular($sParents, $this, $this->calculateOrderCoefficient()->iHotnessCoefficient, $this->sKeepSortedType);
        /*$this->KeepSortedAlgorithmModel->recalculateMostPopular($sParents, $this, $this->calculateOrderCoefficient()->iPublicCoefficient, $this->sKeepSortedType);
        $this->KeepSortedAlgorithmModel->recalculateMostRecent($sParents, $this, $this->calculateOrderCoefficient()->iHotnessCoefficient, $this->sKeepSortedType);*/
    }

    public function removeObjectFromKeepSortedData()
    {
        $sParents = $this->getKeepSortedDataParents();
        if ($sParents == null) return;

        $this->load->model('keep_sorted/keep_sorted_algorithm_model','KeepSortedAlgorithmModel');

        $this->KeepSortedAlgorithmModel->removeMostPopular($sParents, $this);
        $this->KeepSortedAlgorithmModel->removeMostRecent($sParents, $this);
    }

    public function callConstructorsCached($obj=null)
    {
        if ($obj == null) $obj = $this;
        $this->__construct($obj);
    }

    public function cloneObject($object)
    {
        $this->sID = $object->sID;
    }

    static $destructorCopy;

    public function callDestructorCached()
    {
        MY_Advanced_model::$destructorCopy[$this->sID.'sTable'] = $this->sTable;
        //MY_Advanced_model::$destructorCopy[$this->sID.'db'] = $this->db;
        MY_Advanced_model::$destructorCopy[$this->sID.'collection'] = $this->collection;
        MY_Advanced_model::$destructorCopy[$this->sID.'bLastChanged'] = $this->bLastChanged;
        MY_Advanced_model::$destructorCopy[$this->sID.'MaterializedParentsModel'] = $this->MaterializedParentsModel;
        MY_Advanced_model::$destructorCopy[$this->sID.'roleRightsRead'] = $this->roleRightsRead;
        MY_Advanced_model::$destructorCopy[$this->sID.'roleRightsUpdate'] = $this->roleRightsUpdate;
        MY_Advanced_model::$destructorCopy[$this->sID.'roleRightsRemove'] = $this->roleRightsRemove;
        MY_Advanced_model::$destructorCopy[$this->sID.'roleRightsInsert'] = $this->roleRightsInsert;

        if ($this->MaterializedParentsModel != null) {
            MY_Advanced_model::$destructorCopy[$this->sID.'arrMaterializedDataInheritedForUpdate'] = $this->MaterializedParentsModel->arrMaterializedDataInheritedForUpdate;
            unset($this->MaterializedParentsModel->arrMaterializedDataInheritedForUpdate);
        }

        unset($this->sTable, /*$this->db,*/ $this->collection);
        unset($this->bLastChanged, $this->MaterializedParentsModel);
        unset($this->roleRightsRead, $this->roleRightsUpdate, $this->roleRightsRemove, $this->roleRightsInsert);
    }

    public function retrieveBackDestructorCached()
    {
        $this->sTable = MY_Advanced_model::$destructorCopy[$this->sID.'sTable'];
        //$this->db = MY_Advanced_model::$destructorCopy[$this->sID.'db'];
        $this->collection = MY_Advanced_model::$destructorCopy[$this->sID.'collection'];
        $this->bLastChanged = MY_Advanced_model::$destructorCopy[$this->sID.'bLastChanged'];
        $this->MaterializedParentsModel = MY_Advanced_model::$destructorCopy[$this->sID.'MaterializedParentsModel'] ;
        $this->roleRightsRead = MY_Advanced_model::$destructorCopy[$this->sID.'roleRightsRead'] ;
        $this->roleRightsUpdate = MY_Advanced_model::$destructorCopy[$this->sID.'roleRightsUpdate'] ;
        $this->roleRightsRemove = MY_Advanced_model::$destructorCopy[$this->sID.'roleRightsRemove'];
        $this->roleRightsInsert = MY_Advanced_model::$destructorCopy[$this->sID.'roleRightsInsert'];

        if ($this->MaterializedParentsModel != null)
            $this->MaterializedParentsModel->arrMaterializedDataInheritedForUpdate = MY_Advanced_model::$destructorCopy[$this->sID.'arrMaterializedDataInheritedForUpdate'];

    }

}