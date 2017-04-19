<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class View_forum extends  MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('forum/forums_model','ForumsModel');
    }

    public function checkValidAction()
    {
        $string = $this->URI->arrFormParam[count($this->URI->arrFormParam)-1];

        if ($string == 'add-forum-category') $this->URI->sFormAction = array_pop($this->URI->arrFormParam); else
        if ($string == 'edit-forum-category') $this->URI->sFormAction = array_pop($this->URI->arrFormParam); else
        if ($string == 'delete-forum-category') $this->URI->sFormAction = array_pop($this->URI->arrFormParam); else
        if ($string == 'add-forum') $this->URI->sFormAction = array_pop($this->URI->arrFormParam); else
        if ($string == 'edit-forum') $this->URI->sFormAction = array_pop($this->URI->arrFormParam); else
        if ($string == 'delete-forum') $this->URI->sFormAction = array_pop($this->URI->arrFormParam); else
        if ($string == 'add-topic') $this->URI->sFormAction = array_pop($this->URI->arrFormParam); else
        if ($string == 'edit-topic') $this->URI->sFormAction = array_pop($this->URI->arrFormParam); else
        if ($string == 'delete-topic') $this->URI->sFormAction = array_pop($this->URI->arrFormParam);
    }

    public function index()
    {
        //Processing routing formulas
        $this->processRoutingInputParameters(2);

        $Forum = $this->ForumsModel->findForum($this->URI->sFormId, $this->URI->sFormFullURL);

        if ($Forum!= null) {
            if (($this->MyUser->UserActivities != null))
                $this->MyUser->UserActivities->addFastForumClick($Forum->sID,1);

            return $this->renderForumPage($this->URI->sFormFullURL, $Forum, $this->URI->sFormAction, $this->URI->iPageIndex);
        }

        $this->showErrorPage('<strong>Forum not found</strong> '.$this->URI->sFormId.' '.$this->URI->sFormFullURL,'error','forum');
    }

    protected function renderForumPage($sFullURLLink, $Forum,$sAction, $iPageIndex=1)
    {
        $this->Template->MetaController->SchemaMarkup->generateArticleMarkup($Forum->sName, $Forum->sDetailedDescription, $Forum->sImage, $Forum->sDescription, $Forum->getInputKeywordsToString(), $Forum->getAuthorName(), $Forum->getUsedURL(), $Forum->getCreationDateString('c'), $Forum->getLastChangeDateString('c'), '', '', '');
        $this->Template->loadMeta($Forum->sName,$Forum->sDescription, $Forum->sCoverImage, $Forum->getFullURL(), $Forum->getInputKeywordsToString(),$Forum->sLanguage);
        $this->Template->renderHeader('Forums');
        modules::load('fluid_header/forum_header')->index($Forum);

        $this->data['dtForum']=$Forum;

        $this->renderForumBreadcrumbView($Forum);
        $this->renderForumDescriptionView();

        if ($Forum != null)
        switch ($sAction)
        {
            case 'add-forum':
            case 'add-new-forum':
                modules::load('add_forum/add_forum')->index($sAction,$sFullURLLink,$Forum->sParentCategoryId,'');
                break;
            case 'edit-forum':
            case 'delete-forum':
                modules::load('add_forum/add_forum')->index($sAction,$sFullURLLink,$Forum->sParentCategoryId,$Forum);
                break;
            case 'add-forum-category':
            case 'edit-forum-category':
            case 'delete-forum-category':
                modules::load('add_forum_category/add_forum_category')->index($sAction,$sFullURLLink,$Forum,null);
                break;

            case 'add-topic':
            case 'edit-topic':
            case 'delete-topic':
                modules::load('add_topic/add_forum_topic')->index($sAction, $sFullURLLink, $Forum,null);
                break;
        }

        modules::load('display_content/display_top_content_loader')->getTopContentJavaScriptLoader($Forum->sID,$iPageIndex, $iNoElementsCount = 8, $bEnableInfiniteScroll = true,
            $arrInfiniteScrollDisplayContentType = ['topic','forum','f_cat'],  $bEcho = true, $bShowScrollPaginationButtons = true, $bShowFullWidthContainer = true, $sFullWidthContainerTitle = $Forum->sName);

        modules::load('tags/view_tags')->getContainerObject($Forum->getTags());
        modules::load('forum_categories/view_forum_categories')->displayContainer($Forum,'', $iPageIndex, 10, 10, true);
        //$this->renderForumView();

        $this->Template->renderContainer();
        $this->Template->renderFooter();
    }

    protected function renderForumBreadcrumbView($Forum)
    {
        modules::load('breadcrumb/breadcrumb')->addBreadcrumbObject($Forum->getBreadCrumbArray(),2);
    }

    protected function renderForumDescriptionView()
    {
        $this->ContentContainer->addObject($this->renderModuleView('forum_description_view',$this->data,TRUE),'',3);
    }

}