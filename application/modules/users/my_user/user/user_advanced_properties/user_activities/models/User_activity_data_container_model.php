<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * This class stores the user activity (clicks, views) in the database
 */

require_once APPPATH.'core/models/MY_Simple_functions_model.php';
require_once APPPATH.'modules/users/my_user/user/user_advanced_properties/user_activities/models/User_activity_data_model.php';

class User_activity_data_container_model extends MY_Simple_functions_model
{
    public $arrData = array();
    public $sActivityName;
    protected $UserActivities;

    public function __construct($sActivityName, $UserActivities)
    {
        parent::__construct();

        $this->sActivityName = $sActivityName;

        $this->setUserActivitiesParent($UserActivities);
    }

    public function setUserActivitiesParent($UserActivities)
    {
        $this->UserActivities = $UserActivities;
        foreach ($this->arrData as $data)
            $data->UserActivities = $UserActivities;
    }

    public function readCursor($p, $bEnableChildren=null)
    {
        $this->arrData = array();

        foreach ($p as $element)
        {
            $obj = new User_activity_data_model($this->sActivityName, '', $this->UserActivities);
            $obj->readCursor($element,$bEnableChildren);
            array_push($this->arrData, $obj);
        }

    }

    public function serializeProperties()
    {
        $arrResult = array();

        foreach ($this->arrData as $element)
        {
            $arrResult = array_merge($arrResult, array($element));
        }

        return $arrResult;
    }

    public function count()
    {
        return count($this->arrData);
    }

    public function findActivity($sActivityObjectId)
    {
        foreach ($this->arrData as $Activity)
            if ($Activity->sActivityObjectId == $sActivityObjectId)
                return $Activity;
        return null;
    }

    public function insertActivity($ActivityObject)
    {
        if ($this->findActivity($ActivityObject->sActivityObjectId) == null)
            array_push($this->arrData, $ActivityObject);
    }

    public function updateAllContainer()
    {
        $MongoData = array('$set'=>array($this->sActivityName=>$this->serializeProperties()));
        $this->collection->update(array ("_id"=>new MongoId($this->UserActivities->sUserParentId)),$MongoData,array("upsert"=>true));
    }

}
