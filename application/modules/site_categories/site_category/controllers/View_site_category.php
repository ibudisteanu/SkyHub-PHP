<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class View_site_category extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        $this->load->model('site_main_categories/site_categories_model','SiteCategoriesModel');
    }

    public function checkValidAction()
    {
        $string = $this->URI->arrFormParam[count($this->URI->arrFormParam)-1];

        if ($string == 'add-forum') $this->URI->sFormAction = array_pop($this->URI->arrFormParam); else
        if ($string == 'edit-forum-category') $this->URI->sFormAction = array_pop($this->URI->arrFormParam); else
        if ($string == 'delete-forum-category') $this->URI->sFormAction = array_pop($this->URI->arrFormParam); else
        if ($string == 'add-topic') $this->URI->sFormAction = array_pop($this->URI->arrFormParam); else
        if ($string == 'edit-topic') $this->URI->sFormAction = array_pop($this->URI->arrFormParam); else
        if ($string == 'delete-topic') $this->URI->sFormAction = array_pop($this->URI->arrFormParam);
    }

    public function index()
    {
        //Processing routing formulas
        $this->processRoutingInputParameters(2);

        $CategoryObject = $this->SiteCategoriesModel->findCategory($this->URI->sFormId, $this->URI->sFormFullURL);
        if ($CategoryObject != null) {

            if ($this->MyUser->UserActivities != null)
                $this->MyUser->UserActivities->addFastSiteCategoriesClick($CategoryObject->sID,1);

            return $this->viewSiteCategoryPage($CategoryObject, $this->URI->sFormAction, $this->URI->iPageIndex);
        }

        $this->showErrorPage('No category found: '.$this->URI->sFormId.' '.$this->URI->sFormFullURL);
    }

    protected function viewSiteCategoryPage($SiteCategoryObject,$sAction, $iPageIndex)
    {
        $this->Template->MetaController->SchemaMarkup->generateArticleMarkup($SiteCategoryObject->sName, $SiteCategoryObject->sDescription, $SiteCategoryObject->sImage, $SiteCategoryObject->sShortDescription, $SiteCategoryObject->getInputKeywordsToString(), $SiteCategoryObject->getAuthorName(), $SiteCategoryObject->getUsedURL(), $SiteCategoryObject->getCreationDateString('c'), $SiteCategoryObject->getLastChangeDateString('c'), '', '', '');
        $this->Template->loadMeta($SiteCategoryObject->sName, $SiteCategoryObject->sDescription, $SiteCategoryObject->sCoverImage, $SiteCategoryObject->getFullURL(), $SiteCategoryObject->getInputKeywordsToString(), $SiteCategoryObject->sLanguage);

        $this->Template->renderHeader('Forums');
        modules::load('fluid_header/category_header')->index($SiteCategoryObject);

        $this->renderSubCategoryBreadcrumb($SiteCategoryObject);

        switch ($sAction)
        {
            case 'add-forum':
            case 'edit-forum':
            case 'delete-forum':
                modules::load('add_forum/add_forum')->index($sAction, $this->URI->sFormFullURL, $SiteCategoryObject,'');
                break;
            case 'add-topic':
            case 'edit-topic':
            case 'delete-topic':
                modules::load('add_topic/add_forum_topic')->index($sAction, $this->URI->sFormFullURL, $SiteCategoryObject,null);
                break;
        }

        $this->renderSubCategoryView($SiteCategoryObject);


        modules::load('display_content/display_top_content_loader')->getTopContentJavaScriptLoader($SiteCategoryObject->sID,$iPageIndex, $iNoElementsCount = 8,
            $bEnableInfiniteScroll = true, $arrInfiniteScrollDisplayContentType = ['forum','topic'], $bEcho = true, $bShowScrollPaginationButtons = false, $bShowFullWidthContainer = true, $sFullWidthContainerTitle = $SiteCategoryObject->sName);

        $this->Template->renderContainer();
        $this->Template->renderFooter();
    }

    protected function renderSubCategoryBreadcrumb($CategoryObject)
    {
        modules::load('breadcrumb/breadcrumb')->addBreadcrumbObject($CategoryObject->getBreadCrumbArray(),2);
    }

    protected function renderSubCategoryView($CategoryObject)
    {
        $this->data['dtSiteCategory']=$CategoryObject;
        $this->data['dtSiteSubCategories']=$CategoryObject->findTopSubCategories();
        $this->ContentContainer->addObject($this->renderModuleView('site_sub_categories_container',$this->data,TRUE),'<div class="container" style="padding-bottom:20px">',2);
    }


}