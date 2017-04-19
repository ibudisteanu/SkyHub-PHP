<?php

//TUTORIAL https://www.sitepoint.com/bootstrap-tabs-play-nice-with-masonry/

class Data_masonry extends  MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->includeWebPageLibraries('data-masonry');
    }

    public function showMasonryInfiniteScroll($sName='', $sData='', $bHasNext=true, $bHidden=false)
    {
        return $this->showMasonry($sName, $sData, $bHasNext, $bHidden, true);
    }

    public function showMasonry($sName='', $sData='', $bHasNext, $bHidden=false, $bInfiniteScroll=false)
    {
        $this->data['sContentData']=$sData;
        $this->data['sDisplayName']=$sName;
        $this->data['bHasNext'] = $bHasNext;

        if (!$bInfiniteScroll)  $sContent = $this->renderModuleView('data_masonry_view.php',$this->data,true);
        else $sContent = $this->renderModuleView('data_masonry_infinite_scroll_view.php',$this->data,true);

        $this->BottomScriptsContainer->addScript($this->renderModuleView('js/dataMasonryView.js',null,true));

        if ($bHidden) return $sContent;
        else echo $sContent ;
    }

    public function addMasonryContainer($sName='', $sData)
    {
        $this->ContentContainer->addObject($this->showMasonry($sData, true),'<div class="container">',14);
    }
}