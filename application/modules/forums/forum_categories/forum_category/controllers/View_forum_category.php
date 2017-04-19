<?php

class View_forum_category extends  MY_Controller
{
    public $AddTopicInlineController;

    public $bMasonryCategory = false;

    public function __construct()
    {
        parent::__construct();

        $this->load->model('forum/Forums_model','Forums');
        $this->load->model('forum_categories/forum_categories_model','ForumCategoriesModel');
        $this->load->library('StringsAdvanced',null,'StringsAdvanced');

        $this->includeWebPageLibraries('advanced-functions');
        $this->includeWebpageLibraries('file-style');

        $this->AddTopicInlineController =  modules::load('add_topic_inline/add_topic_inline_controller');
    }

    public function index()
    {
        //Processing routing formulas
        $this->processRoutingInputParameters(3);

        $forumCategoriesChildren = $this->ForumCategoriesModel->getForumCategory($this->URI->sFormId, $this->URI->sFormFullURL);

        if ($forumCategoriesChildren == null)
        {
            $this->showErrorPage('No <strong>Forum Category</strong> found: '.$this->URI->sFormId.' '.$this->URI->sFormFullURL);
        } else
        {

            modules::load('voting/voting_controller');


            $this->includeWebPageLibraries('advanced-text-editor');

            if (!$this->MyUser->bLogged)
            {
                $this->PopupAuthentication = modules::load('popup_auth/popup_authentication');
                $this->PopupAuthentication->loadRequirements();
            }

            $this->includeWebPageLibraries('tooltip');

            $this->renderForumCategoryChildrenPage('', $forumCategoriesChildren,$this->URI->sFormAction, max(1,$this->URI->iPageIndex));
        }
    }

    protected function renderForumCategoryChildrenPage($Forum, $forumCategoryChildren, $sAction, $iPageIndex=1)
    {
        $this->Template->MetaController->SchemaMarkup->generateArticleMarkup($forumCategoryChildren->sName, $forumCategoryChildren->sDescription, $forumCategoryChildren->sImage, $forumCategoryChildren->sDescription, $forumCategoryChildren->getInputKeywordsToString(), $forumCategoryChildren->getAuthorName(), $forumCategoryChildren->getUsedURL(), $forumCategoryChildren->getCreationDateString('c'), $forumCategoryChildren->getLastChangeDateString('c'), '', '', '');
        $this->Template->loadMeta($forumCategoryChildren->sName,$forumCategoryChildren->sDescription, $forumCategoryChildren->sCoverImage, $forumCategoryChildren->getFullURL(), $forumCategoryChildren->getInputKeywordsToString(),$forumCategoryChildren->sLanguage);
        $this->Template->renderHeader($forumCategoryChildren->sName);
        modules::load('fluid_header/forum_category_header')->index($forumCategoryChildren);

        switch ($sAction)
        {
            case 'add-forum-category':
            case 'edit-forum-category':
            case 'delete-forum-category':
                modules::load('add_forum_category/add_forum_category')->index($sAction,$this->URI->sFormFullURL, $Forum,$forumCategoryChildren);
                break;
            case 'add-topic':
            case 'edit-topic':
            case 'delete-topic':
                modules::load('add_topic/add_forum_topic')->index($sAction,$this->URI->sFormFullURL,$forumCategoryChildren,null);
                break;
        }
        $this->renderForumCategoryBreadcrumbView($forumCategoryChildren);

        $this->renderForumCategory($forumCategoryChildren, $iPageIndex, $iNumberTopics = 8, true, true, true);

        modules::load('tags/view_tags')->getContainerObject($forumCategoryChildren->getTags());

        $this->Template->renderContainer();
        $this->Template->renderFooter();
    }

    public function renderForumCategory($forumCategory, $iPageIndex, $iNumberTopics, $bEnableScroll=false,$bShowFullWidthContainer=false, $bEcho=false)
    {
        $sObjectID = (is_object($forumCategory) ? $forumCategory->sID : $forumCategory);

        return modules::load('display_content/display_top_content_loader')->getTopContentJavaScriptLoader($sObjectID, $iPageIndex, $iNoElementsCount = $iNumberTopics,
            $bEnableInfiniteScroll = $bEnableScroll, $arrInfiniteScrollDisplayContentType = ['topic'], $bEcho, $bShowScrollPaginationButtons = true,
            $bShowFullWidthContainer, $sFullWidthContainerTitle = $forumCategory->sName);
    }

    protected function renderForumCategoryBreadcrumbView($forumCategoryChildren)
    {
        modules::load('breadcrumb/breadcrumb')->addBreadcrumbObject($forumCategoryChildren->getBreadCrumbArray(),2);
    }

    public function checkValidAction()
    {
        $string = $this->URI->arrFormParam[count($this->URI->arrFormParam)-1];

        if ($string == 'add-forum-category') $this->URI->sFormAction = array_pop($this->URI->arrFormParam); else
        if ($string == 'edit-forum-category') $this->URI->sFormAction = array_pop($this->URI->arrFormParam); else
        if ($string == 'delete-forum-category') $this->URI->sFormAction = array_pop($this->URI->arrFormParam); else
        if ($string == 'add-topic') $this->URI->sFormAction = array_pop($this->URI->arrFormParam); else
        if ($string == 'edit-topic') $this->URI->sFormAction = array_pop($this->URI->arrFormParam); else
        if ($string == 'delete-topic') $this->URI->sFormAction = array_pop($this->URI->arrFormParam);
    }

}