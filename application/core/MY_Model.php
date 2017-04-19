<?php

require_once APPPATH.'modules/users/my_user/user/models/UserRoles.php';
require_once APPPATH.'core/MongoConnection.php';

class MY_Model extends CI_MODEL
{
    public $sID;

    protected $table;
    protected $db;
    protected $collection;

    protected $roleRightsRead=TUserRole::SuperAdmin;
    protected $roleRightsUpdate=TUserRole::SuperAdmin;
    protected $roleRightsInsert=TUserRole::SuperAdmin;
    protected $roleRightsRemove=TUserRole::SuperAdmin;

    public function __construct()
    {
        parent::__construct();

        $this->Parent=null;
        $this->arrChildren=[];

        //$this->load->library('mongo');
        //$this->db = $this->mongo->db;
        //$this->load->library('mongo_db');
        //$this->db=$this->mongo_db;
    }

    public function initDB($table, $rightsRead, $rightsUpdate, $rightsInsert, $rightsRemove)
    {
        $this->table = $table;

        $this->collection = MongoConnection::selectCollection($table);

        $this->roleRightsRead = $rightsRead;
        $this->roleRightsUpdate = $rightsUpdate;
        $this->roleRightsInsert = $rightsInsert;
        $this->roleRightsRemove = $rightsRemove;

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
        if (! TUserRole::checkCompatibility($this->MyUser, $this->roleRightsRead))
        {
            $this->AlertsContainer->addAlert('g_msgGeneralError','error','Not enough rights for the procedure');
            return NULL;
        }

        return $this->collection->findOne($query, $fields);
    }

    public function findByMongoId($sID, $fields=array(), $sQueryFind="_id")
    {
        if (! TUserRole::checkCompatibility($this->MyUser, $this->roleRightsRead))
        {
            $this->AlertsContainer->addAlert('g_msgGeneralError','error','Not enough rights for the procedure');
            return false;
        }

        $IDQuery = array ($sQueryFind=>new MongoId($sID));
        $cursor = $this->collection->findOne($IDQuery, $fields);

        if ($cursor != null)
        {
            $this->loadFromCursor($cursor,false);
            return $this;
        }
        return null;
    }

    public function reload()
    {
        $this->findByMongoId($this->sID);
    }

    public function deleteById($sID, $sQueryFind="_id")
    {
        if (! TUserRole::checkCompatibility($this->MyUser, $this->roleRightsRemove))
        {
            $this->AlertsContainer->addAlert('g_msgGeneralError','error','Not enough rights for deleting the object');
            return false;
        }
        $IDQuery = array ($sQueryFind=>new MongoId($sID));
        $cursor = $this->collection->remove($IDQuery);

        return true;
    }

    public function deleteByChildrenId($sID, $sChildrenID, $sQueryFind="_id", $sQueryChildrenFind = "Children.$._id")
    {
        if (! TUserRole::checkCompatibility($this->MyUser, $this->roleRightsRemove))
        {
            $this->AlertsContainer->addAlert('g_msgGeneralError','error','Not enough rights for deleting the object');
            return false;
        }

        var_dump(array("_id"=>new MongoId($sID)),array("Children._id"=>new MongoId($sChildrenID)),array('$pull',array("Children.$"=>array("_id"=>new MongoId($sChildrenID)))));
        $this->collection->Update(array("_id"=>new MongoId($sID),array("Children"=>array("_id"=>new MongoId($sChildrenID)))),array('$pull',array("Children.$"=>array("_id"=>new MongoId($sChildrenID)))));
        //$this->collection->Update(array("_id"=>new MongoId($sID),array("Children._id"=>new MongoId($sChildrenID))),array('$pull',array("Children.$"=>array("_id"=>new MongoId($sChildrenID)))));
        /*
        //$IDQuery = array ( $sQueryChildrenFind => new MongoId($sChildrenID));
        $IDQuery = array ( "Children.$" => "");
        $ChildrenIDQuery = array ( "Children._id" => new MongoId($sChildrenID));
        var_dump(array ($sQueryFind=>new MongoId($sID)),array('$pull'=>$IDQuery));
        //$this->collection->update(array ($sQueryFind=>new MongoId($sID),$IDQuery),array('$pull'=>$IDQuery));
        $this->collection->update(array ($sQueryFind=>new MongoId($sID),$ChildrenIDQuery),array('$pull'=>$IDQuery));
        */
        return true;
    }

    public function delete($mongoData)
    {
        $this->collection->remove($mongoData);
    }


    public function updateById($sID, $MongoData, $sQueryFind="_id")
    {
        if (! TUserRole::checkCompatibility($this->MyUser, $this->roleRightsUpdate))
        {
            $this->AlertsContainer->addAlert('g_msgGeneralError','error','Not enough rights for updating object');
            return false;
        }

        $this->collection->update(array ($sQueryFind=>new MongoId($sID)),array('$set'=>$MongoData));
        return true;
    }


    public function update($updateQuery, $MongoData, $sCommand='$set', $bUpsert=false)
    {
        if (! TUserRole::checkCompatibility($this->MyUser, $this->roleRightsUpdate))
        {
            $this->AlertsContainer->addAlert('g_msgGeneralError','error','Not enough rights for updating');
            return false;
        }

        //echo json_encode(array($updateQuery,array($sCommand=>$MongoData))).'<br/><br/><br/>';
        if ($bUpsert)
            $this->collection->update($updateQuery,array($sCommand=>$MongoData),array("upsert"=>true));
        else
            $this->collection->update($updateQuery,array($sCommand=>$MongoData));
        return true;
    }

    public function findByName($sName, $fields=array())
    {
        if (! TUserRole::checkCompatibility($this->MyUser, $this->roleRightsRead))
        {
            $this->AlertsContainer->addAlert('g_msgGeneralError','error','Not enough rights for read');
            return false;
        }

        $Query = array ("Name"=>$sName);
        $cursor = $this->collection->find($Query, $fields);
        $count = $cursor->count();

        if ($count == 1)
        {
            $this->loadFromCursor($cursor,false);
            return $this;
        }
        return null;
    }

    public function insertData($MongoData)
    {

        if (! TUserRole::checkCompatibility($this->MyUser, $this->roleRightsInsert))
        {
            $this->AlertsContainer->addAlert('g_msgGeneralError','error','Not enough rights for creating');
            return '-1';
        }

        //print_r($MongoData);
        $this->collection->insert($MongoData);
        $newId = (string)$MongoData['_id'];

        return $newId;
    }

    public function insertDataInside($documentSearch, $subDocumentName, $MongoData)
    {
        if (! TUserRole::checkCompatibility($this->MyUser, $this->roleRightsInsert))
        {
            $this->AlertsContainer->addAlert('g_msgGeneralError','error','Not enough rights for creating');
            return false;
        }

        //print_r(array( '$push' => array( $subDocumentName => $MongoData)));
        //echo "<br/>"."<br/>";
        $this->collection->update( $documentSearch, array( '$push' => array( $subDocumentName => $MongoData)));
        return true;
    }

    protected function readCursor($p)
    {
        $this->sID = (string)reset($p);
    }

    protected function loadFromCursor($cursor,$bEnable=true)
    {
        if (!is_array($cursor))
            foreach ($cursor as $p)
                $this->readCursor($p);
        else
            $this->readCursor($cursor);
    }

}
