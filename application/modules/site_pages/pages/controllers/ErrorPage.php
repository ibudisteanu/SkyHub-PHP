<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ErrorPage extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
    }

    public function displayErrorPage ($sError, $sErrorType='error',$sPageName='home',$sPageTitle=WEBSITE_NAME)
    {
        $this->AlertsContainer->addAlert('g_msgPage'.ucfirst($sErrorType),$sErrorType,$sError);

        if (!$this->MyUser->bLogged)
        {
            $this->homeUnLogged($sPageName, $sPageTitle);
        } else
        {
            $this->homeLogged($sPageName, $sPageTitle);
        }
    }

    protected function homeUnLogged($sPageName, $sPageTitle)
    {
        $this->Template->loadMeta('Error '.$sPageTitle);
        $this->Template->renderHeader($sPageName);

        //modules::run('carousel/carousel/index');
        /*        $counter = modules::load('counter/counter');
                $counter->index('CounterContainer');*/

        modules::run('fluid_header/main_header/index');
        //modules::load('counter/counter')->index();
        $this->ContentContainer->addObject($this->renderModuleView('error_page_view',$this->data,true),'',2);
        modules::load('site_main_categories/view_site_main_categories_controller/index')->index('TopCategoriesContainer');
        modules::run('auth_site/registration/index','RegistrationContainer');

        $this->Template->renderContainer();

        $this->Template->renderFooter();

        //$this->load->view('widgets/gallery_container');
    }

    protected function homeLogged($sPageName, $sPageTitle)
    {
        $this->Template->loadMeta('Error '.$sPageTitle);
        $this->Template->renderHeader($sPageName);

        $this->ContentContainer->addObject($this->renderModuleView('error_page_view',$this->data,true),'',2);

        modules::run('fluid_header/main_header/index');
        modules::load('site_main_categories/view_site_main_categories_controller/index')->index('TopCategoriesContainer');

        $this->Template->renderContainer();
        $this->Template->renderFooter();
    }

}