<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * This class stores the user activities (clicks, views) in the database
 */

require_once APPPATH.'core/models/MY_Simple_functions_model.php';
require_once APPPATH.'modules/users/my_user/user/user_advanced_properties/user_activities/models/User_activity_data_container_model.php';

class User_activities_model extends MY_Simple_functions_model
{

    public $TopicsActivitiesContainer = null;
    public $ForumsActivitiesContainer = null;
    public $ForumsCategoriesActivitiesContainer = null;
    public $SiteCategoriesActivitiesContainer = null;
    public $arrActivitiesContainers = [];

    protected $bLoaded=false;

    //sID is actually UserId

    public function __construct($sUserParentId, $bReadAll=true)
    {
        parent::__construct();
        $this->initDB('UsersActivities',TUserRole::User,TUserRole::User,TUserRole::User,TUserRole::User);

        if ($sUserParentId != '')  $this->sID = (string) $sUserParentId;
        else $this->sID = 'xxx';

        $this->TopicsActivitiesContainer = new User_activity_data_container_model('TopicsActiv', $this);
        $this->ForumsActivitiesContainer = new User_activity_data_container_model('ForumsActiv', $this);
        $this->ForumsCategoriesActivitiesContainer = new User_activity_data_container_model('ForumsCategActiv', $this);
        $this->SiteCategoriesActivitiesContainer = new User_activity_data_container_model('SiteCategActiv', $this);

        $this->arrActivitiesContainers = [$this->TopicsActivitiesContainer, $this->ForumsActivitiesContainer, $this->ForumsCategoriesActivitiesContainer, $this->SiteCategoriesActivitiesContainer];

        if ($bReadAll == true)
            $this->readAll();
    }

    public function readAll()
    {
        $fields = array();

        $sCacheId = 'readAllUserActivities_'.$this->sID;

        if (!$result = $this->AdvancedCache->get($sCacheId )){

            $result = $this->findOne(array("_id"=>new MongoId($this->sID)),$fields);
            if ($result != null) {
                $this->readCursor($result);
                $this->saveCacheData();

            }
        } else
        {
            $this->TopicsActivitiesContainer = $result['Topics'];
            $this->ForumsActivitiesContainer = $result['Forums'];
            $this->ForumsCategoriesActivitiesContainer = $result['Forum_cat'];
            $this->SiteCategoriesActivitiesContainer = $result['Site_cat'];

            foreach ($this->arrActivitiesContainers as $Activity)
                $Activity->setUserActivities($this);
        }

        $this->bLoaded=true;
    }

    public function saveCacheData()
    {
        $sCacheId = 'readAllUserActivities_'.$this->sID;

        foreach ($this->arrActivitiesContainers as $Activity) $Activity->setUserActivitiesParent(null);

        $result = ["Topics"=>$this->TopicsActivitiesContainer, "Forums"=>$this->ForumsActivitiesContainer,
            "Forum_cat"=>$this->ForumsCategoriesActivitiesContainer, "Site_cat"=>$this->SiteCategoriesActivitiesContainer];

        if ($sCacheId != '')  $this->AdvancedCache->save($sCacheId, $result, 2678400);

        foreach ($this->arrActivitiesContainers as $Activity) $Activity->setUserActivitiesParent($this);
    }

    public function readCursor($p, $bEnableChildren=null)
    {
        parent::readCursor($p, $bEnableChildren);

        if (isset($p["TopicsActiv"])) {
            $this->TopicsActivitiesContainer->readCursor($p["TopicsActiv"], $bEnableChildren);
        }

        if (isset($p["ForumsActiv"])) {
            $this->ForumsActivitiesContainer->readCursor($p["ForumsActiv"], $bEnableChildren);
        }

        if (isset($p['ForumsCategActiv'])){
            $this->ForumsCategoriesActivitiesContainer->readCursor($p["ForumsCategActiv"], $bEnableChildren);
        }

        if (isset($p["SiteCategActiv"])) {
            $this->SiteCategoriesActivitiesContainer->readCursor($p["SiteCategActiv"], $bEnableChildren);
        }
    }

    public function serializeProperties()
    {
        $arrResult = array();

        if (($this->TopicsActivitiesContainer != null && ($this->TopicsActivitiesContainer->count() > 0)))
            $arrResult = array_merge($arrResult, array("TopicsActiv"=>$this->TopicsActivitiesContainer));

        if (($this->ForumsActivitiesContainer != null && ($this->ForumsActivitiesContainer->count() > 0)))
            $arrResult = array_merge($arrResult, array("ForumsActiv"=>$this->ForumsActivitiesContainer));

        if (($this->SiteCategoriesActivitiesContainer != null && ($this->SiteCategoriesActivitiesContainer->count() > 0)))
            $arrResult = array_merge($arrResult, array("SiteCategActiv"=>$this->SiteCategoriesActivitiesContainer));

        if (($this->ForumsCategoriesActivitiesContainer!= null && ($this->ForumsCategoriesActivitiesContainer->count() > 0)))
            $arrResult = array_merge($arrResult, array("ForumCategActiv"=>$this->ForumsCategoriesActivitiesContainer));

        return $arrResult;
    }

    protected function findActivityDataInArray($ContainerName, $objectId='' )
    {
        if (!isset($this->$ContainerName))
            return [new User_activity_data_model('topic', $objectId, $this), false];

        $Activity = $this->$ContainerName->findActivity($objectId);
        if ($Activity == null)  return [new User_activity_data_model('topic', $objectId, $this),false];
        return [$Activity, true];
    }

    protected function getContainerNameFromActivityName($sActivityName='')
    {
        switch ($sActivityName)
        {
            case 'TopicsActiv':
                return 'TopicsActivitiesContainer';
            case 'SiteCategActiv':
                return 'SiteCategoriesActivitiesContainer';
            case 'ForumsActiv':
                return 'ForumsActivitiesContainer';
            case 'ForumsCategActiv':
                return 'ForumsCategoriesActivitiesContainer';
        }
        return '';
    }

    protected function findActivityData($sActivityName='', $objectId='')
    {
        if ($this->bLoaded)
        {
            return $this->findActivityDataInArray($this->getContainerNameFromActivityName($sActivityName),$objectId);

        } else
        {
            $cursor = $this->findOne(array("_id" => new MongoId($this->sID), $sActivityName . ".ActivityObjectId" => new MongoId($objectId)));
            $ActivityData = null;

            $ActivityData = new User_activity_data_model($sActivityName, $objectId, $this);
            if ($cursor != null) {
                if (isset($cursor[$sActivityName]))
                    if (isset($cursor[$sActivityName][0])) {
                        $ActivityData->readCursor($cursor[$sActivityName][0], true);
                        return array($ActivityData, false);
                    }
            }
            return array($ActivityData, true);
        }
    }

    public function insertActivity($sActivityName, $ActivityObject, $bInsert=false)
    {
        if ($this->bLoaded)
        {
            $ContainerName = $this->getContainerNameFromActivityName($sActivityName);

            $this->$ContainerName->insertActivity($ActivityObject);

            $this->saveCacheData();
        } else
        {
            if ($bInsert)
            {
                $MongoData = array('$set'=>array($sActivityName=>array($ActivityObject->serializeProperties())));
                $this->collection->update(array ("_id"=>new MongoId($this->sID)),$MongoData,array("upsert"=>true));
            } else
                $ActivityObject->updateActivityDB();
        }

    }

    public function addFastObjectClick($sObjectName="TopicsActiv",$sObjectId, $iValue=1)
    {
        $data = $this->findActivityData($sObjectName,$sObjectId);
        $ActivityData = $data[0];
        $ActivityData->addActivityClick($iValue);
        $this->insertActivity($sObjectName,$ActivityData, $data[1]);
    }

    public function addFastObjectView($sObjectName="TopicsActiv", $sObjectId, $iValue=1)
    {
        $data = $this->findActivityData($sObjectName,$sObjectId);
        $ActivityData = $data[0];
        $ActivityData->addActivityView($iValue);
        $this->insertActivity($sObjectName,$ActivityData, $data[1]);
    }

    public function addFastTopicClick($sObjectId, $iValue=1){
        $this->addFastObjectClick("TopicsActiv", $sObjectId, $iValue);
    }
    public function addFastTopicView($sObjectId, $iValue=1){
        $this->addFastObjectView("TopicsActiv", $sObjectId, $iValue);
    }
    public function getFastTopicActivity($sObjectId){
        return $this->findActivityData("TopicsActiv",$sObjectId)[0];
    }




    public function addFastSiteCategoriesClick($sObjectId, $iValue=1){
        $this->addFastObjectClick("SiteCategActiv", $sObjectId, $iValue);
    }
    public function addFastSiteCategoriesView($sObjectId, $iValue=1){
        $this->addFastObjectView("SiteCategActiv", $sObjectId, $iValue);
    }
    public function getFastSiteCategoryActivity($sObjectId){
        return $this->findActivityData("SiteCategActiv",$sObjectId)[0];
    }


    public function addFastForumClick($sObjectId, $iValue=1){
        $this->addFastObjectClick("ForumsActiv", $sObjectId, $iValue);
    }
    public function addFastForumView($sObjectId, $iValue=1){
        $this->addFastObjectView("ForumsActiv", $sObjectId, $iValue);
    }
    public function getFastForumActivity($sObjectId){
        return $this->findActivityData("ForumsActiv",$sObjectId)[0];
    }


    public function addFastForumCategoryClick($sObjectId, $iValue=1){
        $this->addFastObjectClick("ForumsCategActiv", $sObjectId, $iValue);
    }
    public function addFastForumCategoryView($sObjectId, $iValue=1){
        $this->addFastObjectView("ForumsCategActiv", $sObjectId, $iValue);
    }
    public function getFastForumCategoryActivity($sObjectId){
        return $this->findActivityData("ForumsCategActiv",$sObjectId)[0];
    }

}
