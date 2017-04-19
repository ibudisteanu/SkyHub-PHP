<?php

class MY_Simple_functions_model extends MY_Advanced_model
{

    protected $table;
    protected $db;
    protected $collection;

    public $sID;

    public function __construct()
    {
        $this->sID = (string) new MongoId();

        parent::__construct();
    }


    protected function readCursor($p, $bEnableChildren=null)
    {
        $this->sID = (string)reset($p);
    }

    protected function serializeProperties()
    {
        $arrResult = array();

        if ($this->sID == '')
            $this->sID = (string) new MongoId();

        $arrResult = array_merge($arrResult, array("_id" => new MongoId($this->sID)));

        return $arrResult;
    }

    /* OLD FUNCTIONS */

    public function update($updateQuery, $MongoData, $sCommand='$set', $bUpsert=false)
    {

        //echo json_encode(array($updateQuery,array($sCommand=>$MongoData))).'<br/><br/><br/>';
        if ($bUpsert)
            $this->collection->update($updateQuery,array($sCommand=>$MongoData),array("upsert"=>true));
        else
            $this->collection->update($updateQuery,array($sCommand=>$MongoData));
        return true;
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

}