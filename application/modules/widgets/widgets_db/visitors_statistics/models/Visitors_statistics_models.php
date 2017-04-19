<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once APPPATH.'modules/widgets/widgets_db/visitors_statistics/models/Visitors_statistics_model.php';

class Visitors_statistics_models extends Visitors_statistics_model
{
    public $sClassName = 'Visitors_statistics_model';

    public function __construct($bEnableChildren=true)
    {
        parent::__construct($bEnableChildren);
    }

    public function findVisitorsStatisticsByAttachedParentId($sAttachedParentId='')
    {
        $sCacheId = 'findVisitorsStatisticsByAttachedParentId_'.$this->sAttachedParentId;

        if (($sCacheId=='')||(!$statistics = $this->AdvancedCache->get($sCacheId)))
        {
            $statistics = $this->loadContainerByFieldName("AttachedParentId",new MongoId($sAttachedParentId));
            if ($statistics == null) {
                $statistics = new Visitors_statistics_model($sAttachedParentId);
                $statistics->sAttachedParentId = $sAttachedParentId;
                $statistics->saveCache();
            }
        } else
            $statistics->__construct($sAttachedParentId);

        return $statistics;
    }

}