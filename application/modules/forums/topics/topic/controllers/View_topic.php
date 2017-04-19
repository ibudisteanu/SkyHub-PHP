<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class View_topic extends  MY_Controller
{
    public $PopupAuthentication;

    public $AddReplyInlineController;
    public $AddTopicInlineController;

    public function __construct()
    {
        parent::__construct();

        //$this->load->model('forum/forums_model','ForumsModel');
        $this->load->model('topics/topics_model', 'TopicsModel');
        $this->load->model('reply/replies_model','RepliesModel');

        $this->AddReplyInlineController =  modules::load('add_reply_inline/add_reply_inline_controller');
        $this->AddTopicInlineController =  modules::load('add_topic_inline/add_topic_inline_controller');

        if (!$this->MyUser->bLogged)
        {
            $this->PopupAuthentication = modules::load('popup_auth/popup_authentication');
            $this->PopupAuthentication->loadRequirements();
        }

        $this->includeWebPageLibraries('tooltip');

        $this->ViewAvatarController = modules::load('user/View_avatar');
        $this->VotingController =  modules::load('voting/voting_controller');
        $this->includeWebPageLibraries('scrolling');
        $this->includeWebPageLibraries('country-select');
    }

    public function checkValidAction()
    {
        $string = $this->URI->arrFormParam[count($this->URI->arrFormParam)-1];

        //echo 'action'.$string;

        if ($string == 'edit-topic') $this->URI->sFormAction = array_pop($this->URI->arrFormParam); else
        if ($string == 'delete-topic') $this->URI->sFormAction = array_pop($this->URI->arrFormParam);
    }

    public function viewPage() // The index of the controller
    {
        //Processing routing formulas
        $this->processRoutingInputParameters(2);

        $Topic = $this->TopicsModel->getTopic($this->URI->sFormId, $this->URI->sFormFullURL);

        if ($Topic != null) {

            if ($this->MyUser->UserActivities != null)
                $this->MyUser->UserActivities->addFastTopicClick($Topic->sID,1);

            $Topic->increaseViews();

            return $this->renderForumPage($this->URI->sFormFullURL, $Topic, $this->URI->sFormAction);
        }

        $this->showErrorPage('No topic found: '.$this->URI->sFormId.' '.$this->URI->sFormFullURL);
    }

    public function renderForumPage($sFullURLLink, $Topic, $sAction)
    {
        if ($Topic == null)
        {
            $this->showErrorPage('No Topic found');
        }
        $this->Template->MetaController->SchemaMarkup->generateArticleMarkup($Topic->sTitle, $Topic->getBodyCodeRendered(), $Topic->objImagesComponent->getImagesArray(true), $Topic->sShortDescription, $Topic->getInputKeywordsToString(), $Topic->getAuthorName(), $Topic->getUsedURL(), $Topic->getCreationDateString('c'), $Topic->getLastChangeDateString('c'), '', '', '');
        $this->Template->loadMeta($Topic->sTitle,$Topic->sShortDescription, $Topic->objImagesComponent->getImagesArray(true), $Topic->getUsedURL(), $Topic->getInputKeywordsToString(), $Topic->sLanguage);

        $this->Template->renderHeader('Topic');
        modules::load('fluid_header/Topic_header')->index($Topic);

        $this->data['dtTopic']=$Topic;
        $RepliesContainer = $this->RepliesModel->findTopRepliesByAttachedParentId($Topic->sID);
        $this->data['dtRepliesContainer'] = $RepliesContainer;

        switch ($sAction)
        {
            case 'edit-topic':
            case 'delete-topic':
                modules::load('add_topic/add_forum_topic')->index($sAction,$sFullURLLink,null,$Topic);
                break;
            default :
                $this->renderTopicQuestionView($Topic, true);
                break;
        }

        $this->renderTopicBreadcrumbView($Topic);

        $this->renderTopicCommentsView();

        //View Topic Comments
        //$this->renderForumView();

        $this->Template->renderContainer();
        $this->Template->renderFooter();
    }

    protected function renderTopicBreadcrumbView($Topic)
    {
        modules::load('breadcrumb/breadcrumb')->addBreadcrumbObject($Topic->getBreadCrumbArray(),2);
    }

    protected function getReplyOwner($Topic)
    {
        if (($this->MyUser->sID != '') &&(($this->MyUser->sID == $Topic->sAuthorId) || (TUserRole::checkUserRights(TUserRole::Admin))))  return true;
        else return false;
    }

    public function renderTopicQuestionView($Topic, $bDisplay = true)
    {
        $this->data['dtTopic'] = $Topic;
        $this->data['bReplyOwner'] = $this->getReplyOwner($Topic);

        $sContent = $this->load->view('topic/topic_question_view',$this->data,TRUE);

        if ($bDisplay) $this->ContentContainer->addObject($sContent,'',3);
        else return $sContent;
    }

    public function renderTopicBody($Topic, $bDisplay = false)
    {
        $this->data['dtTopic'] = $Topic;
        $this->data['bReplyOwner'] = $this->getReplyOwner($Topic);

        $sContent = $this->load->view('topic/topic_question_body_view',$this->data,TRUE);

        if ($bDisplay) $this->ContentContainer->addObject($sContent,'',3);
        else return $sContent;
    }

    protected function renderTopicCommentsView()
    {
        $this->ViewReplyController = modules::load('reply/View_reply');
        $this->ContentContainer->addObject($this->load->view('topic/topic_replies_view',$this->data,TRUE),'',5);
    }

}