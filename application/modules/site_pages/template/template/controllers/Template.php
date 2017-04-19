<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Template extends MY_Controller
{
    public $data;
    public $MetaController;

    public $RightSideBar;
    public $LeftSideBar;

    function __construct()
    {
        parent::__construct();

        //$this->Header= modules::load('Template/Header');
        $this->load->helper('url');

        $this->load->vars(array('g_sThemeURL' => rtrim(base_url('theme/'),'/').'/'));
        $this->load->vars(array('g_sAdminTitle' => WEBSITE_NAME.' Admin'));

        $this->MetaController = modules::load('meta/meta_controller', NULL);
    }

    public function createRightSidebar()
    {
        $this->RightSideBar = modules::load('right_sidebar/right_sidebar_menu', NULL);
        $this->RightSideBar->index();
    }

    public function renderRightSidebar()
    {
        $this->RightSideBar->renderMenu();
    }

    public function renderLeftSidebar()
    {
        if (!$this->data['g_bSideBarDisabled'])
        {
            $this->LeftSideBar = modules::load('left_sidebar/left_sidebar_menu', NULL);
            $this->LeftSideBar ->index();
            $this->LeftSideBar ->renderMenu();
        }
    }

    protected function renderNav()
    {
        modules::run('nav_menu/nav_menu/index', NULL);
        modules::run('user_menu/user_menu/index', NULL);
        $this->load->view('template/template_nav_view',$this->data);
    }

    public function renderHeader($sActivePage)
    {
        if ($this->MyUser->bLogged)  $this->data['g_bSideBarDisabled']=false;
        else $this->data['g_bSideBarDisabled']=true;


        $this->load->vars(array('g_sActivePage' => $sActivePage));
        $this->load->vars(array('g_sTitle' => $this->MetaController->getMetaTitle()) );

        if ((TUserRole::checkUserRights(TUserRole::Admin))||(isset($_COOKIE['disableSkyHubAnalytics'])))
            $this->data['g_bHideAnalytics']=true;

        $this->createRightSidebar();

        $this->load->view('template/template_head_view', $this->data);
        $this->renderNav();
        $this->renderLeftSidebar();
        $this->renderRightSidebar();

    }

    public function renderFooter()
    {
        modules::run('footer_menu/footer_menu/index', NULL);
        $this->load->view('template/template_footer_end_view');
    }

    public function loadMeta($sMetaTitle='', $sMetaDescription ='', $arrMetaImages=null, $sMetaURL = '', $sMetaKeywords='', $sMetaLanguage='', $sMetaPageType='')
    {
        $this->MetaController->loadMeta($sMetaTitle, $sMetaDescription, $arrMetaImages, $sMetaURL, $sMetaKeywords, $sMetaLanguage, $sMetaPageType);
    }


    public function renderContainer($bContainer=false)
    {
        //$this->Header->index();
        $this->data['bContainer'] = $bContainer;
        $this->load->view('template/template_container_view', $this->data);
    }

    public function scrollToElementId($sId)
    {
        $this->BottomScriptsContainer->addScript('$(document).ready(function() { }',true);
    }

}