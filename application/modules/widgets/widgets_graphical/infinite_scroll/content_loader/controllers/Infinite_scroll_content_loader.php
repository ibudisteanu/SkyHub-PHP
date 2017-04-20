<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// TUTORIAL INFINITE SCROLL https://www.sitepoint.com/seo-friendly-infinite-scroll/

class Infinite_scroll_content_loader extends  MY_Controller
{

    protected  $bEnableMasonry = true;

    public function __construct($bDisableTemplate = false)
    {
        parent::__construct($bDisableTemplate);
    }

    public function initializeScrollLoader($sInfiniteScrollActionName, $bEnableInfiniteScroll=true, $sInfiniteScrollContainerName, $sParentId, $iPageIndex, $iOriginalPageIndex, $iPageElementsCount=30,
                                           $bHasNext, $arrInfiniteScrollDisplayContentType=true, $bShowScrollPaginationButtons=false, $bEnableMasonry=false, $dtContent = '', $bEcho=false, $sPaginationSuffix='page' )
    {
        if ($sParentId  == '') $sParentId  = 'all';

        $sInfiniteScrollContainerName = $sInfiniteScrollContainerName . (string)rand(0,100)."_loadingParentId_".$sParentId;
        $sReturn = '';

        $this->bEnableMasonry = $bEnableMasonry;

        if ($iPageIndex < 1 ) $iPageIndex = 1;

        if (($bHasNext) || ($dtContent != '')) {
            $data['sInfiniteScrollContainerName'] = $sInfiniteScrollContainerName;
            $data['dtContent'] = $dtContent;
            $data['bHasNext'] = $bHasNext;
            $data['bEnableMasonry'] = $this->bEnableMasonry;

            if ($bEnableMasonry)
                $sReturn .= $this->renderModuleView('infinite_scroll_content_loader_masonry_view.php', $data, true);
            else
                $sReturn .= $this->renderModuleView('infinite_scroll_content_loader_view.php', $data, true);
        }

        if ($bHasNext) {
            $sReturn .= modules::load('pagination/pagination')->renderPagination($iIndex = $iOriginalPageIndex, $sPaginationSuffix = $sPaginationSuffix, $bShowScrollPaginationButtons, false);

            $this->executeNormal($sInfiniteScrollContainerName, $bEnableInfiniteScroll, $sParentId, $iPageIndex, $iPageElementsCount, $bHasNext, $arrInfiniteScrollDisplayContentType, $sInfiniteScrollActionName);
        }

        $this->BottomScriptsContainer->addScriptResFile(base_url(defined('WEBSITE_OFFLINE') ? "app/res/js/infinite-scroll-content-loader.js" : 'assets/min-js/infinite-scroll-content-loader-min.js'), 'none', true);

        if (!$bEcho) return $sReturn;
        else {
            echo $sReturn;
            return '';
        }
    }

    private function executeNormal($sInfiniteScrollContainerName, $bEnableInfiniteScroll, $sParentId, $iPageIndex, $iPageElementsCount, $bHasNext, $arrInfiniteScrollDisplayContentType, $sInfiniteScrollActionName)
    {
        $data['sInfiniteScrollContainerName'] = $sInfiniteScrollContainerName;
        $data['iPageIndex'] = $iPageIndex;
        $data['sParentId'] = $sParentId;
        $data['bHasNext'] = $bHasNext;
        $data['iPageElementsCount'] = $iPageElementsCount;
        $data['sInfiniteScrollActionName'] = $sInfiniteScrollActionName;
        $data['bEnableInfiniteScroll'] = $bEnableInfiniteScroll;
        $data['arrInfiniteScrollDisplayContentType'] = $arrInfiniteScrollDisplayContentType;

        $this->BottomScriptsContainer->addScript($this->renderModuleView('js/initializeInfiniteScrollContentLoader.js.php',$data,true),true,'none',true);

    }
}