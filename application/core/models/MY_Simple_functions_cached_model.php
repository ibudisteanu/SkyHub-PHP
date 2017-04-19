<?php

require_once APPPATH.'core/models/MY_Simple_functions_model.php';

class MY_Simple_functions_cached_model extends  MY_Simple_functions_model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertDataCached($MongoData)
    {

        parent::insertData($MongoData);
    }

    public function deleteByChildrenIdCached($sCacheId='', $sID, $sChildrenID, $sQueryFind="_id", $sQueryChildrenFind = "Children.$._id")
    {
        parent::deleteByChildrenId($sID, $sChildrenID, $sQueryFind, $sQueryChildrenFind);
    }

    public function insertDataInsideCached($sCacheId='', $documentSearch, $subDocumentName, $MongoData)
    {
        parent::insertDataInside($documentSearch, $subDocumentName, $MongoData);
    }

    public function updateCached($sCacheId='', $updateQuery, $MongoData, $sCommand='$set', $bUpsert=false)
    {
        parent::update($updateQuery, $MongoData, $sCommand, $bUpsert);
    }

}