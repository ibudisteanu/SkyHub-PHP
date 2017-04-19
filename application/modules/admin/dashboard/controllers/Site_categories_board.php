<?php

class Site_categories_board extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function index($sActionName='add-category', $sID='')
    {
        if (TUserRole::checkUserRights(TUserRole::Admin))
        {
            $this->renderPage($sActionName, $sID);
        } else
        {
            //redirect('/login/form/', 'refresh');
            show_404();
        }
    }


    public function renderPage($sActionName, $sID)
    {
        $this->Template->loadMeta('Site Categories','Add Site Categories on the '.WEBSITE_TITLE);
        $this->Template->renderHeader('Forums');
        $this->ContentContainer->addObject($this->renderModuleView('dashboard',null,true));

        switch ($sActionName)
        {
            case '':
            case 'add-category':
            case 'add-subcategory':
                modules::load('add_edit_site_category/add_site_category')->index($sActionName,$sID, null);
                break;
            case 'edit-category':
            case 'delete-category':
                modules::load('add_edit_site_category/add_site_category')->index($sActionName,null, $sID);
                break;
        }

        $this->Template->renderContainer();
        $this->Template->renderFooter();
    }
}