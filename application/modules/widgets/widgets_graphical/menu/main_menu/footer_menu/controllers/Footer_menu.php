<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Footer_menu extends MY_Controller
{
    public $navMenu;

    public function index()
    {
        //Check the User
        if ($this->MyUser->bLogged)
        {
            $this->navMenu = $this->userLoggedInMenu();
        } else
            $this->navMenu = $this->notLoggedInMenu();



        $this->load->vars(array('g_objFooterUserMenu' => $this));
    }

    protected function notLoggedInMenu()
    {
        return array(
            array('Home', base_url('')),
            array('Login', base_url('#loginbox')),
            array('Register', base_url('#Registration')),
            array('Forums', base_url('')),
            array('Topics', base_url('all-topics')),
            array('About', base_url('about/#About')),
            array('Contact', base_url('about/#Contact'))
        );
    }

    protected function userLoggedInMenu()
    {
        return array(
            array('Home', base_url('')),
            array($this->MyUser->sFirstName.' ('.$this->MyUser->sUserName.')',base_url('profile/'.$this->MyUser->getUserLink())),
            array('Logout', base_url('/logout')),
            array('Forums', base_url('news/edit')),
            array('About', base_url('about/#About')),
            array('Contact', base_url('about/#Contact'))
        );
    }

    protected function adminLoggedInMenu()
    {

    }

    public function renderMenu()
    {
        foreach ($this->navMenu as $menuItem)
        {
            $this->load->vars(array('g_NavItem' => $menuItem));
            $this->load->view('footer_menu/footer_menu_item_view');
        }
    }

}