<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Display_preview_parent_controller extends MY_Controller
{
    public $AddTopicInlineController;
    public $FromTopicPreviewController;
    public function __construct()
    {
        parent::__construct();

        $this->load->model('forum/forum_model','ForumModel');

        $this->AddTopicInlineController =  modules::load('add_topic_inline/add_topic_inline_controller');

        $this->FromTopicPreviewController =  modules::load('forum_topic_preview/view_forum_topic_preview');
        $this->ForumPreviewController = modules::load('forum_preview/forum_preview');
    }

    public function renderPreviewTopicsArrayParentBox($sParentId, &$arrTopicsWitSameParent, $bEcho=false)
    {
        if (!is_array($arrTopicsWitSameParent)) $arrTopicsWitSameParent = array($arrTopicsWitSameParent);

        $objParent = $this->AdvancedCache->getObjectFromId($sParentId);

        if ($objParent != null)
        {
            $sParentClassName = get_class($objParent);

            $this->data['sFullURL']=$objParent->getFullURL();
            if (isset($objParent->sCoverImage)) $this->data['sCoverImage']=$objParent->sCoverImage;
            if (isset($objParent->sImage)) $this->data['sImage']=$objParent->sImage;
            if (isset($objParent->sName)) $this->data['sName']=$objParent->sName;
            if (isset($objParent->sDescription)) $this->data['sDescription']=$objParent->sDescription;

            switch ($sParentClassName)
            {
                case 'Forum_model':
                case 'Site_category_model':
                    break;
                    break;
            }
        }

        $this->data['boxSize']='col-md-12 col-sm-12 col-xs-12 col-xxs-12 col-tn-12 item';
        $this->data['boxStyle'] = 'padding: 10px 10px 10px 0px; left: 0px; top: 0px;';

        $sContent  = '';
        foreach ($arrTopicsWitSameParent as $topic)
        {
            //$sContent .= $this->DisplayPreviewParentController->renderPreviewForumView($forum, $data, $iPageIndex, $iNumberTopics, false);
        }
        $this->data['sContent'] = $sContent;

        $result = $this->renderModuleView('display_preview_parent_box_view',$this->data,TRUE);

        if ($bEcho) $this->ContentContainer->addObject($result);
        else return $result;
    }


    public function renderPreviewTopicsArrayParentTable($sParentId, &$arrTopicsWitSameParent, $bMasonryItem = false, $bEcho=false)
    {
        if ($arrTopicsWitSameParent == null) return '';

        if (!is_array($arrTopicsWitSameParent)) $arrTopicsWitSameParent = array($arrTopicsWitSameParent);

        $objParent = $this->AdvancedCache->getObjectFromId($sParentId);

        $this->data['objParent'] = $objParent;
        $this->data['sParentId'] = ''; $this->data['sName'] = ''; $this->data['sDescription'] = ''; $this->data['sImage'] = ''; $this->data['sCoverImage'] = ''; $this->data['sFullURL'] ='';

        if ($objParent != null)
        {
            $sParentClassName = get_class($objParent);

            $this->data['sParentId'] = $objParent->sID;
            $this->data['sFullURL']=$objParent->getFullURL();
            if (isset($objParent->sCoverImage)) $this->data['sCoverImage']=$objParent->sCoverImage;
            if (isset($objParent->sImage)) $this->data['sImage']=$objParent->sImage;

            if (isset($objParent->sName)) $this->data['sName']=$objParent->sName;
            elseif (isset($objParent->sTitle)) $this->data['sName']=$objParent->sTitle;

            if (isset($objParent->sDescription)) $this->data['sDescription']=$objParent->sDescription;

        }

        $this->data['bMasonryItem']=$bMasonryItem;


        $sContent  = '';
        foreach ($arrTopicsWitSameParent as $topic)
            if (($topic != null) && ($topic != $sParentId))
            {
                //var_dump($topic ->objRepliesComponent->iNoReplies  .' '. $topic ->objRepliesComponent->iNoUsersReplies);
                //var_dump($topic ->calculateOrderCoefficient());
                $sContent .= $this->FromTopicPreviewController->renderPreviewForumTopicView($topic, true);
            }
        $this->data['topicsContent'] = $sContent;

        $result = $this->renderModuleView('display_preview_parent_table_view',$this->data,TRUE);

        if ($bEcho) $this->ContentContainer->addObject($result);
        else return $result;
    }

    public function renderPreviewForumArray($sParentId, $arrForumWithSameParent, $iPageIndex, $iNumberOfCategories, $iNumberOfTopics, $bEcho=false)
    {
        if ($arrForumWithSameParent == null) return '';

        if (!is_array($arrForumWithSameParent)) $arrForumWithSameParent = array($arrForumWithSameParent);
        $result = '';

        $data = [];
        $data['boxSize'] = 'col-md-12 col-sm-12 col-xs-12 col-xxs-12 col-tn-12 item '; //item enables masonry
        $data['boxStyle'] = 'padding: 0 0 25px 0; left: 0px; top: 0px;';

        foreach ($arrForumWithSameParent as $forum)
            if (($forum != null)&&(is_object($forum))){
                $result .= $this->ForumPreviewController->renderPreviewForumView($forum, $data, $iPageIndex, $iNumberOfCategories, $iNumberOfTopics, false);
            }

        if ($bEcho) $this->ContentContainer->addObject($result);
        else return $result;
    }

}