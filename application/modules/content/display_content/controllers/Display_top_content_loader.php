<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Display_top_content_loader extends MY_Controller
{

    public $bEnableMasonry = false;

    function __construct()
    {
        parent::__construct();

        $this->includeWebPageLibraries('advanced-functions');
        $this->includeWebpageLibraries('file-style');
        $this->includeWebPageLibraries('advanced-text-editor');
        $this->includeWebPageLibraries('tooltip');
        $this->includeWebPageLibraries('country-select');

        /*$this->BottomScriptsContainer->addScriptResFile(base_url("app/res/js/files-upload-system.js"));
        $this->includeWebPageLibraries('jqueryFileUpload');*/

        modules::load('add_topic_inline/add_topic_inline_controller');
        modules::load('add_reply_inline/add_reply_inline_controller');
    }

    // TUTORIAL INFINITE SCROLL https://www.sitepoint.com/seo-friendly-infinite-scroll/
    public function getTopContentJavaScriptLoader($sParentId='', $iPageIndex=1, $iNoElementsCount=8, $bEnableInfiniteScroll=true, $arrInfiniteScrollDisplayContentType=['forum','topic'], $bEcho=false,
                                                  $bShowScrollPaginationButtons=false, $bShowFullWidthContainer=true, $sFullWidthContainerTitle=null, $sFullWidthContainerSubTitle=null)
    {
        $this->VotingController =  modules::load('voting/voting_controller');

        if (!$this->MyUser->bLogged) {
            $this->PopupAuthentication = modules::load('popup_auth/popup_authentication');
            $this->PopupAuthentication->loadRequirements();
        }

        $iPageIndex = (int) $iPageIndex;
        $iOriginalPageIndex = $iPageIndex;

        $bHasNext = true;
        if (!$bEnableInfiniteScroll) $bHasNext=false;

        $sTopContent = '';
        if ($iPageIndex > 0){

            $TopContentController = modules::load('top_content/top_content_controller');

            $arrIDsAlreadyUsed = [];
            $result = $TopContentController->renderTopContentFromParent($sParentId, $arrIDsAlreadyUsed, $iPageIndex, $iNoElementsCount, $arrDisplayContentType = $arrInfiniteScrollDisplayContentType, $bHidden = true);

            $sTopContent.=$result['sContent'];

            if (!$result['bHasNext']) $bHasNext = false;

            $iPageIndex++;
        } else
        $iPageIndex = 1;

        if ($bHasNext) {
            $sTopContent .=
                modules::load('content_loader/Infinite_scroll_content_loader')->initializeScrollLoader('getTopContent', $bEnableInfiniteScroll, 'TopContentCategory', $sParentId,
                    $iPageIndex, $iOriginalPageIndex, $iNoElementsCount, $bHasNext, $arrInfiniteScrollDisplayContentType, $bShowScrollPaginationButtons, $this->bEnableMasonry,
                    (isset($dtTopContent) ? $dtTopContent : ''), false, 'page');
        }

        if ($bShowFullWidthContainer) {
            $this->data['sTopContent'] = $sTopContent;
            $this->data['sContainerTitle'] = $sFullWidthContainerTitle;
            $this->data['sContainerSubTitle'] = $sFullWidthContainerSubTitle;

            $sContent = $this->renderModuleView('display_top_content_container', $this->data, TRUE);
        }
        else
            $sContent = $sTopContent;

        if ($bEcho)  $this->ContentContainer->addObject($sContent,'',5);
        else return $sContent;
    }

}