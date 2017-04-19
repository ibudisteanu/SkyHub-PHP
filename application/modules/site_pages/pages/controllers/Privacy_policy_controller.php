<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Privacy_policy_controller extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
    }

    public function index ($iPageIndex=0)
    {
        if (!$this->MyUser->bLogged)
        {
            $this->homeNotLogged($iPageIndex);
        } else
        {
            $this->homeLogged($iPageIndex);
        }
    }



    protected function homeNotLogged($iPageIndex=0)
    {
        $this->Template->loadMeta(WEBSITE_TITLE);
        $this->Template->renderHeader('home');

        //modules::run('carousel/carousel/index');
        /*        $counter = modules::load('counter/counter');f
                $counter->index('CounterContainer');*/

        modules::run('fluid_header/main_header/index');
        modules::load('counter/counter')->index();
        modules::run('auth_site/registration/index','RegistrationContainer');

        $this->ContentContainer->addObject($this->renderModuleView('privacy_policy_view',$this->data, TRUE),'',2);

        $this->Template->renderContainer();

        $this->Template->renderFooter();

        //$this->load->view('widgets/gallery_container');
    }

    protected function homeLogged($iPageIndex=0)
    {
        $this->Template->loadMeta(WEBSITE_TITLE);
        $this->Template->renderHeader('home');

        modules::run('fluid_header/profile_header/index');

        $this->ContentContainer->addObject($this->renderModuleView('privacy_policy_view',$this->data, TRUE),'',2);

        $this->Template->renderContainer();

        $this->Template->renderFooter();
    }

}