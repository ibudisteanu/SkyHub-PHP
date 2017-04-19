<?php

class Top_content_models extends MY_Model
{
    /*  Returning the Top Content from this Parent*/
    public function findTopContent($Parent, &$arrIDsAlreadyUsed, $iPageIndex=1, $iNoTopicsCount=20, $arrDisplayContentType=['topics'])
    {
        if ($iNoTopicsCount <=0 ) return [];

        $iNoPopular = $iNoTopicsCount-3;
        $iNoRecent = 0 ; //$iNoRecent=($iNoTopicsCount-($iNoPopular))/3;
        $iNoPersonal=$iNoTopicsCount-($iNoPopular+$iNoRecent);

        $this->load->model('keep_sorted/Keep_sorted_algorithm_model','KeepSortedAlgorithm');
        $arrTopContentIds = $this->KeepSortedAlgorithm->getSortedElementsHotness($Parent, $arrIDsAlreadyUsed, $iPageIndex, $iNoPopular, $iNoPersonal, $arrDisplayContentType);

        return $this->AdvancedCache->getObjectsFromIds($arrTopContentIds);
    }

    /*  Returning the top Topics from this parent this Parent*/
    public function findTopTopics($Parent, &$arrIDsAlreadyUsed, $iPageIndex=1, $iNoTopicsCount=20)
    {
        $this->load->model('topics/topics_model','TopicsModel');
        return $this->TopicsModel->findTopTopics($Parent, $arrIDsAlreadyUsed, $iPageIndex, $iNoTopicsCount);
    }

}