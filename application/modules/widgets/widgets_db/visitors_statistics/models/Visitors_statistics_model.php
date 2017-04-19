<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'core/models/MY_Hierarchy_model.php';

class Visitors_statistics_model extends MY_Hierarchy_model
{
    public $sClassName = 'Visitors_statistics_model';
    protected $sParentArrayChildrenName;

    public $updateByQueryMethod;

    protected $iViews=0;
    protected $iSeen=0;
    protected $arrIPVisitors = [];

    //$sAttachedParentId for keeping track of the attached id;

    public function __construct($sAttachedParentId='', $updateByQuery=null, $sParentArrayChildrenName='')
    {
        parent::__construct(false);

        if ($updateByQuery == null) {
            $this->initDB('VisitorsStatistics', TUserRole::notLogged, TUserRole::notLogged, TUserRole::Admin, TUserRole::SuperAdmin);
            $this->updateByQueryMethod = array($this, "updateByQuery");
        }
        else
            $this->updateByQueryMethod = $updateByQuery;

        $this->sParentArrayChildrenName = $sParentArrayChildrenName;
        $this->sAttachedParentId = (string)$sAttachedParentId;
    }

    public function getNumberUniqueVisitors()
    {
        return count($this->arrIPVisitors);
    }

    public function getNumberViews()
    {
        return $this->iViews;
    }

    public function getNumberSeen()
    {
        return $this->iSeen;
    }

    public function increaseNumberViews($iValue = 1, $bDoRefresh = true)
    {
        //it also increase Seen

        if ($this->sAttachedParentId == '') return false;

        $this->iViews += $iValue;

        $bIPAdded = $this->addVisitorIP('',false);

        if ($bDoRefresh == true)
        {
            $updateByQueryMethod = $this->updateByQueryMethod;

            if ($this->sParentArrayChildrenName != '') {
                $arrNewData = array($this->sParentArrayChildrenName.".$.Visitors.Views" => $this->iViews);
                if ($bIPAdded)  $arrNewData = array_merge($arrNewData, array($this->sParentArrayChildrenName.".$.Visitors.IPVisitors"=>$this->arrIPVisitors));

                //var_dump([$this->sParentArrayChildrenName . "._id" => new MongoId($this->sAttachedParentId)]); var_dump($arrNewData);
                try {
                    $updateByQueryMethod([$this->sParentArrayChildrenName . "._id" => new MongoId($this->sAttachedParentId)], $arrNewData, true);
                }
                catch (Exception $ex) {

                }

            }
            else {
                $arrNewData = array("Visitors.Views" => $this->iViews);
                if ($bIPAdded)  $arrNewData = array_merge($arrNewData, array("Visitors.IPVisitors"=>$this->arrIPVisitors));

                try {
                    $updateByQueryMethod(["_id" => new MongoId($this->sAttachedParentId)], $arrNewData, true);
                }
                catch (Exception $ex) {

                }
            }
        }

        $this->saveCache();
    }

    public function increaseNumberSeen($iValue = 1, $bDoRefresh = true)
    {
        if ($this->sAttachedParentId == '') return false;

        $this->iSeen += $iValue;

        if ($bDoRefresh) {
            $updateByQueryMethod = $this->updateByQueryMethod;

            if ($this->sParentArrayChildrenName != '')
                $updateByQueryMethod([$this->sParentArrayChildrenName . "._id" => new MongoId($this->sAttachedParentId)], array($this->sParentArrayChildrenName . ".$.Visitors.Seen" => $this->iSeen), true);
            else
                $updateByQueryMethod(["_id" => new MongoId($this->sAttachedParentId)], array("Visitors.Seen" => $this->iSeen), true);
        }

        $this->saveCache();
    }

    protected function addVisitorIP($sIP = '', $bDoRefresh=true)
    {
        if ($sIP == '') {
            $this->load->model('ip/ip','IP');
            $sIP = $this->IP->sIP;
        }

        if (!$this->checkVisitorIP($sIP)) return false;

        array_push($this->arrIPVisitors, $sIP);

        if ($bDoRefresh == true)
        {
            $this->updateByQuery(["AttachedParentId" => new MongoId($this->sAttachedParentId)], array("IPVisitors" => $this->arrIPVisitors), true);
            $this->saveCache();
        }
        return true;
    }

    protected function checkVisitorIP($sIPtoBeChecked = '')
    {
        if ($sIPtoBeChecked == '') return false;

        //var_dump($sIPtoBeChecked);

        foreach ($this->arrIPVisitors as $sIP)
            if ($sIP == $sIPtoBeChecked) return false;

        return true;
    }

    public function readCursor($p, $bEnableChildren = null)
    {
        parent::readCursor($p);

        if (isset($p['Views'])) $this->iViews = $p['Views'];
        else $this->iViews = 0;

        if (isset($p['Seen'])) $this->iSeen = $p['Seen'];
        else $this->iSeen = 0;

        if (isset($p['IPVisitors'])) $this->arrIPVisitors = $this->recoverIPVisitors($p['IPVisitors']);
    }

    public function serializeProperties()
    {
        $arrResult = parent::serializeProperties();

        if (($this->iViews > 0)) $arrResult = array_merge($arrResult, array("Views" => $this->iViews));
        if (($this->iSeen > 0)) $arrResult = array_merge($arrResult, array("Seen" => $this->iViews));

        if (count($this->arrIPVisitors) > 0)
            $arrResult = array_merge($arrResult, array("IPVisitors" => $this->minimizeIPVisitors()));

        return $arrResult;
    }

    protected function recoverIPVisitors($arrIPVisitors)
    {
        $arrResult = [];
        foreach ($arrIPVisitors as $IPVisitor)
            if (is_string($IPVisitor))
                array_push($arrResult,$IPVisitor);
            else
                array_push($arrResult, long2ip($IPVisitor));

        return $arrResult;
    }

    protected function minimizeIPVisitors()
    {
        $arrResult = [];
        foreach ($this->arrIPVisitors as $IPVisitor)
            array_push($arrResult,new MongoInt32(ip2long($IPVisitor)));

        return $arrResult;
    }


    protected function saveCache()
    {
        /*$sCacheId = 'findVisitorsStatisticsByAttachedParentId_'.$this->sAttachedParentId;
        $this->AdvancedCache->save($sCacheId, $this, 2678400);*/
    }

    public function resetCache()
    {
        $this->AdvancedCache->delete('findVisitorsStatisticsByAttachedParentId_'.$this->sAttachedParentId);
    }

}

