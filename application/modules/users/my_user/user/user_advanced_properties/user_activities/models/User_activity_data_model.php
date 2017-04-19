<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * This class stores the user activity (clicks, views) in the database
 */

require_once APPPATH.'core/models/MY_Simple_functions_model.php';

class User_activity_data_model extends MY_Simple_functions_model
{
    public $sActivityName; // activity parent name

    public $sActivityObjectId;
    public $iActivityClicks=0;
    public $iActivityViews=0;
    public $dtLastActivity; //for decaying

    public $UserActivities;

    public function __construct($sActivityName, $sActivityObjectId, $UserActivities)
    {
        parent::__construct();

        $this->sActivityObjectId = $sActivityObjectId;
        $this->sActivityName = $sActivityName;
        $this->UserActivities = $UserActivities;
    }

    public function readCursor($p, $bEnableChildren=null)
    {
        if (isset($p['ActivityObjectId'])) $this->sActivityObjectId = (string) $p['ActivityObjectId'];

        if (isset($p["ActivityClicks"])) $this->iActivityClicks = $p['ActivityClicks'];
        else $this->iActivityClicks = 0;

        if (isset($p["ActivityViews"])) $this->iActivityViews = $p['ActivityViews'];
        else $this->iActivityViews=0;

        if (isset($p["LastActivityDate"])) $this->dtLastActivity = $p['LastActivityDate'];
        else $this->dtLastActivity = null;

    }

    public function serializeProperties()
    {
        $arrResult = array();

        $arrResult = array_merge($arrResult, array("ActivityObjectId"=>new MongoId($this->sActivityObjectId)));
        if ((isset($this->sActivityObjectId)))

        if ((isset($this->sActivityObjectId))&&($this->iActivityClicks != 0) )
            $arrResult = array_merge($arrResult, array("ActivityClicks"=>$this->iActivityClicks));

        if ((isset($this->iActivityViews))&&($this->iActivityViews != 0))
            $arrResult = array_merge($arrResult, array("ActivityClicks"=>$this->iActivityViews));

        if ((isset($this->dtLastActivity))&&($this->dtLastActivity != null))
            $arrResult = array_merge($arrResult, array("LastActivityDate"=>$this->dtLastActivity));

        return $arrResult;
    }

    public function addActivityClick($iValue=+1)
    {
        $this->iActivityClicks += $iValue;
        $this->updateLastActivityDate();
    }

    public function addActivityView($iValue = +1)
    {
        $this->iActivityViews += $iValue;
        $this->updateLastActivityDate();
    }

    protected function updateLastActivityDate()
    {
        $this->load->library('TimeLibrary',null,'TimeLibrary');
        $this->dtLastActivity =  $this->TimeLibrary->getDateTimeNowUnixMongoDate();
    }

    public function updateActivityDB()
    {
        $MongoSearch = array ("_id"=>new MongoId($this->UserActivities->sUserParentId),($this->sActivityName.".ActivityObjectId")=>new MongoId($this->sActivityObjectId));
        $MongoData = array($this->sActivityName.'.$' => $this->serializeProperties());

        $this->update($MongoSearch,$MongoData,'$set',true);
    }

}
