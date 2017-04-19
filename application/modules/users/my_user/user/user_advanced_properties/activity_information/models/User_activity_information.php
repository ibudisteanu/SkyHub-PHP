<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * This class stores additional properties in the User Table about Creation date and the Last Activity (Login)
 */

//require_once APPPATH.'core/models/MY_Advanced_model.php';

class User_activity_information extends CI_Model
{
    public $dtLastLogin;
    public $dtLastActivity;

    public $sUserParentId;
    private $UserCollection;
    private $m_enUserStatus=null;

    public function __construct($sUserParentId, $collection)
    {
        $this->sUserParentId=$sUserParentId;
        $this->UserCollection = $collection;
    }

    public function readCursor($p, $bEnableChildren=null)
    {
        if (isset($p["LastLogin"])) $this->dtLastLogin = $p['LastLogin'];
        else $this->dtLastLogin=null;

        if (isset($p["LastActivity"])) $this->dtLastActivity = $p['LastActivity'];
        else $this->dtLastActivity=null;
    }

    public function serializeProperties()
    {
        $arrResult = array();

        $arrResult = array_merge($arrResult, array("LastLogin"=>$this->dtLastLogin));
        $arrResult = array_merge($arrResult, array("LastActivity"=>$this->dtLastActivity));

        return $arrResult;
    }

    public function updateLastLogin()
    {
        $this->load->library('TimeLibrary',null,'TimeLibrary');
        $this->dtLastLogin =  $this->TimeLibrary->getDateTimeNowUnixMongoDate();
    }

    public function updateLastActivity()
    {
        $this->load->library('TimeLibrary',null,'TimeLibrary');
        $this->dtLastActivity =  $this->TimeLibrary->getDateTimeNowUnixMongoDate();
    }

    public function updateOnlyLastLogin()
    {
        $this->load->library('TimeLibrary',null,'TimeLibrary');
        $this->dtLastLogin =  $this->TimeLibrary->getDateTimeNowUnixMongoDate();
        $MongoData = array('$set'=>array("LastLogin"=>$this->dtLastLogin));
        $this->collection->update(array ("_id"=>new MongoId($this->sUserParentId)),$MongoData,array("upsert"=>true));
    }

    public function updateOnlyLastActivity()
    {
        $this->load->library('TimeLibrary',null,'TimeLibrary');
        $this->dtLastActivity =  $this->TimeLibrary->getDateTimeNowUnixMongoDate();
        $MongoData = array('$set'=>array("LastActivity"=>$this->dtLastActivity));
        $this->UserCollection->update(array ("_id"=>new MongoId($this->sUserParentId)),$MongoData,array("upsert"=>true));
    }

    public function getUserStatus()
    {
        if ($this->m_enUserStatus == null)
        {
            $this->load->library('TimeLibrary',null,'TimeLibrary');
            $this->m_enUserStatus = TUserStatus::getUserStatusFromDate($this->dtLastActivity, $this->TimeLibrary);
        }
        return $this->m_enUserStatus;
    }



}
