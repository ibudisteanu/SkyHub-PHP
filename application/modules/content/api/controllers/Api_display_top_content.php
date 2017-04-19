<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api_display_top_content extends MY_Controller
{

    public $bEnableMasonry = false;

    function __construct()
    {
        parent::__construct();

        $this->includeWebPageLibraries('advanced-functions');
        $this->includeWebpageLibraries('file-style');

        modules::load('add_topic_inline/add_topic_inline_controller');
        modules::load('add_reply_inline/add_reply_inline_controller');
    }

    public function getTopContentJSON($sParentId='', $iPageIndex=1, $iNoTopicsCount=20, $bHidden=false, $bEnableMasonry=false)
    {
        $arrIDsAlreadyUsed = []; $arrInfiniteScrollDisplayContentType=[];
        if (isset($_POST['arrIDsAlreadyUsed'])) $arrIDsAlreadyUsed = $_POST['arrIDsAlreadyUsed'];
        if (isset($_POST['arrInfiniteScrollDisplayContentType'])) $arrInfiniteScrollDisplayContentType = $_POST['arrInfiniteScrollDisplayContentType'];

        $iPageIndex = (int) $iPageIndex;
        if ($sParentId == 'all') $sParentId = '';

        $TopContentController = modules::load('top_content/top_content_controller');

        $result = $TopContentController->renderTopContentFromParent($sParentId, $arrIDsAlreadyUsed, $iPageIndex, $iNoTopicsCount, $arrDisplayContentType = $arrInfiniteScrollDisplayContentType, $bHidden = true);

        if ($result['sContent'] != '')
            $array = ["result"=>true,"finished"=>!$result['bHasNext'],"enableMasonry"=>$bEnableMasonry,"content"=>$result['sContent'],
                'voteActivation'=>$this->BottomScriptsContainer->findScriptByName('voteActivation',true),'arrIDsAlreadyUsedNew'=>$arrIDsAlreadyUsed ];
        else
            $array =["result"=>false,"finished"=>!$result['bHasNext'],"enableMasonry"=>$bEnableMasonry,"content"=>'','arrIDsAlreadyUsedNew'=>''];

        $sContent = json_encode($array,true);
        echo $sContent;
        return $sContent;
    }

}