<?php

class Header extends  MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->helper('url');

        $var = $this->load->get_var('g_User');
        if (!isset($var))
        {
            $this->load->vars(array('g_User' => $this->MyUser));
        }
    }

    function index ($sPage='')
    {
        if (!$this->MyUser->bLogged)
        {
            $this->headerUnLogged();
        } else
        {
            $this->headerLoggedMe();
        }
    }

    protected function headerUnLogged()
    {
        $Login = modules::load('auth_site/login/');
        $Login->index('box');

        $this->renderModuleView('header_home',$this->data);
    }

    protected function headerLoggedMe()
    {
        $this->renderModuleView('header_profile',$this->data);
    }

}