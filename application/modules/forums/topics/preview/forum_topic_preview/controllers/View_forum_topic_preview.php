<?php

class View_forum_topic_preview extends MY_Controller
{
    public $ViewAvatarController;
    public $VotingController;

    public $AddReplyInlineController;
    public $AddTopicInlineController;

    public function __construct()
    {
        parent::__construct();

        $this->ViewAvatarController = modules::load('user/View_avatar',null,'ViewAvatarController');

        $this->load->model('forum/forum_model','ForumModel');
        $this->VotingController = modules::load('voting/voting_controller');
        $this->AddReplyInlineController =  modules::load('add_reply_inline/add_reply_inline_controller');
        $this->AddTopicInlineController =  modules::load('add_topic_inline/add_topic_inline_controller');
    }

    public function renderPreviewForumTopicView($Topic, $bHidden=false, $bJustBody=false)
    {
        if (is_string($Topic))
        {
            $this->load->model('forum/forums_model','ForumsModel');
            $Topic = $this->ForumsModel->findForumById($Topic);
        }

        if ($Topic == null)
        {
            $this->AlertsContainer->addAlert('g_msgGeneralWarning','warning','<b>No Topic found</b>');
            return '';
        }

        if (get_class($Topic)!='Topic_model')
        {
            $this->AlertsContainer->addAlert('g_msgGeneralWarning','warning','Topic sent '.get_class($Topic).' is not actually Topic');
            return '';
        }

        $sCacheId = 'renderPreviewForumTopicView_'.$Topic->sID; $sContent='';

        //if (!$sContent = $this->AdvancedCache->get($sCacheId ))
        {
            $this->data['dtTopicPreview']=$Topic;
            if (($this->MyUser->sID != '') &&(($this->MyUser->sID == $Topic->sAuthorId) || (TUserRole::checkUserRights(TUserRole::Admin))))
                $this->data['bReplyOwner'] = true;
            else
                $this->data['bReplyOwner'] = false;

            if (!$bJustBody) $sContent = $this->renderModuleView('forum_topic_preview_view',$this->data,TRUE);
            else $sContent = $this->renderModuleView('forum_topic_preview_body_view',$this->data,TRUE);

            //if ($sCacheId != '')  $this->AdvancedCache->save($sCacheId, $sContent , 2678400);
        }

        if (!$bHidden) $this->ContentContainer->addObject($sContent );
        else return $sContent;
    }

    public function renderPreviewForumTopicBody($Topic, $bHidden=false)
    {
        return $this->renderPreviewForumTopicView($Topic, $bHidden, true);
    }

}