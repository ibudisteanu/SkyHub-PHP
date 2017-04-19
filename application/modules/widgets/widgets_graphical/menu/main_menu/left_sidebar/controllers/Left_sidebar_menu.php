<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'modules/widgets/widgets_graphical/menu/main_menu/nav_menu/models/NavItem.php';

class Left_sidebar_Menu extends MY_Controller
{
    public $navMenu;

    public function index()
    {
        //Check the User

        if ($this->MyUser->bLogged)
        {
            if (TUserRole::checkUserRights(TUserRole::Admin))
                $this->navMenu = $this->adminLoggedInMenu();
            else
                $this->navMenu = $this->userLoggedInMenu();
        } else
            $this->navMenu = $this->notLoggedInMenu();
    }

    protected function notLoggedInMenu()
    {
        //No array
        return array();
    }

    protected function userLoggedInMenu()
    {
        return array(
            new NavItem('Network',base_url(''),'fa fa-globe',[],0),
            new NavItem('Forums',base_url(''),'fa fa-commenting',[],0),
            new NavItem('Profile','','glyphicon glyphicon-user',
                [
                    new NavItem($this->MyUser->sFirstName.' ('.$this->MyUser->sUserName.')',base_url('profile/'.$this->MyUser->getUserLink()),'glyphicon glyphicon-user',[],1),
                    new NavItem('Edit Profile',base_url('profile/edit'),'glyphicon glyphicon-pencil',[],1),
                    new NavItem('Log Out',base_url('logout'),'fa fa-power-off',[],1),
                ],1),
            new NavItem('Earnings','#','fa fa-dollar',[],0),
            new NavItem('Messages','#','fa fa-envelope-o',
                [
                    new NavItem('Inbox',base_url('#'),'fa fa-folder-open-o',[],1),
                ],0,false,'pull-right bg-yellow','0'),
            new NavItem(WEBSITE_NAME,base_url('about'),'',[],0,true),
            new NavItem('Contact',base_url('about/#Contact'),'glyphicon glyphicon-envelope',[],0),
        );
    }

    protected function adminLoggedInMenu()
    {
        $menu = $this->userLoggedInMenu();
        $adminMenu = new NavItem('Admin',base_url(''),'fa fa-gear',[
            new NavItem('Dashboard',base_url('admin'),'fa fa-gears',[],1),
            new NavItem('Site Categories',base_url('admin/site/categories'),'fa fa-comments',[],1),
            new NavItem('Emails Board',base_url('admin/site/emails'),'fa fa-envelope',[],1),
            new NavItem('App Database',base_url('admin/apps/db'),'fa fa-database',[],1),
        ],1);

        $adminMenu2 = new NavItem(WEBSITE_NAME,base_url(''),'',[],0,true);
        array_unshift($menu,$adminMenu,$adminMenu2);


        return  $menu;
    }

    public function renderMenu()
    {
        $this->load->view('left_sidebar/left_sidebar_view');
    }


}