<?php

class Signin extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
    }

    function index ()
    {
        if (!$this->MyUser->bLogged)
        {
            $this->showLoginContainer();
        } else
        {
            redirect(base_url(''), 'refresh');
        }
    }


    protected function showLoginContainer()
    {
        $this->Template->loadMeta(WEBSITE_TITLE);
        $this->Template->renderHeader('home');


        modules::run('fluid_header/main_header/index');
        //modules::load('auth_site/login')->index('page');

        modules::run('auth_site/registration/index','RegistrationContainer');

        $this->Template->renderContainer();


        $this->Template->renderFooter();

        //$this->load->view('widgets/gallery_container');
    }



}