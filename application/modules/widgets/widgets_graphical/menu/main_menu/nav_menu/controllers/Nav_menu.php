<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'modules/widgets/widgets_graphical/menu/main_menu/nav_menu/models/NavItem.php';

class Nav_menu extends MY_Controller
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

        $this->load->vars(array('g_objNavigationNavMenu' => $this));
    }

    protected function notLoggedInMenu()
    {
        //No array
        return array(
            new NavItem('Search',base_url(''),'fa fa-globe',[],0),
            new NavItem('Login', base_url('#loginbox'),'fa fa-key',[],0,false,'','','return openLoginPopupAuthentication()'),
            new NavItem('Register',base_url('#Registration'),'fa fa-globe',[],0,false,'','','return openRegistrationPopupAuthentication()'),
            new NavItem('Forums',base_url(''),'fa fa-commenting',[],0),

            /*
            new NavItem('About',base_url('about/#Contact'),'fa fa-globe',
                [
                    new NavItem('About',base_url('about/#About'),'fa fa-globe',[],1),
                    new NavItem('Contact',base_url('about/#Contact'),'glyphicon glyphicon-envelope',[],1),
                ],0),
            */
        );
    }

    protected function userLoggedInMenu()
    {
        return array(
            new NavItem('Search',base_url(''),'fa fa-globe',[],0),
            new NavItem('Forums',base_url(''),'fa fa-commenting',[],0),
        );
    }

    /*protected function adminLoggedInMenu()
    {
        $adminMenu = array(
            new NavItem('Admin',base_url(''),'',[],0,true),
            new NavItem('Dashboard',base_url('admin'),'fa faEdit',[],0),
            new NavItem('Forums Board',base_url('admin'),'fa faEdit',[],0),
            new NavItem('Emails Board',base_url('emails'),'fa faEdit',[],0),
        );
        return  array_push($this->userLoggedInMenu(),$adminMenu);
    }*/

    public function renderMenu()
    {

        foreach ($this->navMenu as $menuItem)
        {
            $this->load->vars(array('g_NavItem' => $menuItem));

            $this->load->view('nav_menu/nav_item_view');
        }
    }


}