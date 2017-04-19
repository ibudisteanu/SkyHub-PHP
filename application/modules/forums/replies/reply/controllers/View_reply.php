<?php

class View_reply extends  MY_Controller
{
    public $PopupAuthentication;
    public $AddReplyInlineController;
    public $VotingController;
    public $ViewAvatarController;
    public $ViewReplyController;

    public function __construct()
    {
        parent::__construct();

        //$this->load->model('forum/forums_model','ForumsModel');

        $this->ViewAvatarController = modules::load('user/View_avatar');
        $this->AddReplyInlineController =  modules::load('add_reply_inline/add_reply_inline_controller');
        $this->VotingController =  modules::load('voting/voting_controller');
        $this->includeWebPageLibraries('tooltip');
        $this->ViewReplyController = $this;

        if (!$this->MyUser->bLogged)
        {
            $this->PopupAuthentication = modules::load('popup_auth/popup_authentication');
            $this->PopupAuthentication->loadRequirements();
        }

    }

    public function renderReply($Reply, $DisplayAdsAlgorithmController=null, $bHidden=true, $bDrawMinimalReplies=false)
    {
        if ($Reply == null) return;

        $this->data['DisplayAdsAlgorithmController'] = $DisplayAdsAlgorithmController;

        $this->data['dtReply'] = $Reply;
        $this->data['bDrawMinimalReplies'] = $bDrawMinimalReplies;

        if (($this->MyUser->sID != '') &&(($this->MyUser->sID == $Reply->sAuthorId) || (TUserRole::checkUserRights(TUserRole::Admin))))
            $this->data['bReplyOwner']= true;
        else
            $this->data['bReplyOwner'] = false;

        return $this->renderModuleView('reply_view',$this->data,$bHidden);
    }

}