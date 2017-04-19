<?php

class Add_topic_inline_controller extends  MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        //$this->load->model('forum_category/forum_categories_model','ForumCategories');

        $this->load->library('StringsAdvanced',null,'StringsAdvanced');

        $this->initializeTopicInlineController();
    }

    public function initializeTopicInlineController()
    {
        $this->BottomScriptsContainer->addScriptResFile(base_url(defined('WEBSITE_OFFLINE') ? "app/res/js/topic-inline-functions.js" : 'assets/min-js/topic-inline-functions-min.js'));
        $this->BottomScriptsContainer->addScriptResFile(base_url(defined('WEBSITE_OFFLINE') ? "app/res/js/add-edit-forum-topic-functions.js" : 'assets/min-js/add-edit-forum-topic-functions-min.js'));

        $this->includeWebpageLibraries('file-style');
        $this->includeWebPageLibraries('advanced-functions');
        $this->includeWebPageLibraries('advanced-text-editor');
    }

    public function renderTopicAddButton($sParentId, $sFormIndex, $sFormResponseType = 'topic-preview')
    {
        /*
         * $sFormResponseType can be:
         *     topic-preview
         *     topic-preview-table
         */


        $this->data['sParentId'] = $sParentId;
        $this->data['sFormIndex']= $sFormIndex;
        $this->data['sFormResponseType'] = $sFormResponseType;

        return $this->renderModuleView('add_topic_button_view',$this->data,TRUE);
    }

    public function renderTopicDeleteButton($sTopicId, $sTopicTitle)
    {
        $this->data['sTopicId'] = $sTopicId;
        $this->data['sTopicTitle'] = $sTopicTitle;

        return $this->renderModuleView('delete_topic_button_view',$this->data,TRUE);
    }

    public function renderTopicEditButton($sParentId, $sTopicId, $sTopicTitle, $sFormResponseType = 'topic-preview')
    {
        $this->data['sTopicId'] = $sTopicId;
        $this->data['sParentId'] = $sParentId;
        $this->data['sTopicTitle'] = $sTopicTitle;
        $this->data['sFormResponseType'] = $sFormResponseType;

        return $this->renderModuleView('edit_topic_button_view',$this->data,TRUE);
    }

}