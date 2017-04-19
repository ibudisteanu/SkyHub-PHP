<?php

class Add_reply_inline_controller extends  MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        //$this->load->model('forum_category/forum_categories_model','ForumCategories');

        $this->initializeReplyInlineController();
    }

    public function initializeReplyInlineController()
    {
        $this->load->library('StringsAdvanced',null,'StringsAdvanced');
        //$this->BottomScriptsContainer->addScriptResFile(base_url(defined('WEBSITE_OFFLINE') ? "app/res/js/reply-inline-functions.js" : 'assets/min-js/reply-inline-functions-min.js.php'));
        $this->BottomScriptsContainer->addScriptResFile(base_url("app/res/js/reply-inline-functions.js" ));
        $this->includeWebPageLibraries('advanced-text-editor');
        $this->includeWebPageLibraries('advanced-functions');
    }

    public function renderReplyAddButton($sReplyParentObjectId, $sReplyParentObjectTitle, $sReplyGrandFatherId)
    {
        $this->data['replyParentObjectId'] = $sReplyParentObjectId;
        $this->data['replyParentObjectTitle']= $sReplyParentObjectTitle;
        $this->data['replyGrandFatherObjectId'] = $sReplyGrandFatherId;
        return $this->renderModuleView('add_reply_button_view',$this->data,TRUE);
    }

    public function renderReplyDeleteButton($sReplyObjectId, $sReplyObjectTitle, $sReplyGrandFatherId)
    {
        $this->data['replyObjectId'] = $sReplyObjectId;
        $this->data['replyObjectTitle']= $sReplyObjectTitle;
        $this->data['replyGrandFatherObjectId'] = $sReplyGrandFatherId;
        return $this->renderModuleView('delete_reply_button_view.php',$this->data,TRUE);
    }

    public function renderReplyEditButton($sReplyObjectId, $sReplyObjectTitle, $sReplyGrandFatherId)
    {
        $this->data['replyObjectId'] = $sReplyObjectId;
        $this->data['replyObjectTitle']= $sReplyObjectTitle;
        $this->data['replyGrandFatherObjectId'] = $sReplyGrandFatherId;
        return $this->renderModuleView('edit_reply_button_view.php',$this->data,TRUE);
    }

}