<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'modules/widgets/widgets_graphical/menu/main_menu/nav_menu/models/NavItem.php';

class User_Menu extends MY_Controller
{

    public $nav;
    public $NotificationsController;

    public function __construct()
    {
        parent::__construct();

    }

    public function index()
    {
        //Check the User

        $this->load->vars(array('g_objUserMenu' => $this));
    }

    protected function notLoggedInMenu()
    {
        return [];
    }

    public function renderMenu()
    {
        if ($this->MyUser->bLogged)
        {
            $this->load->view('user_menu/user_buddy_requests_menu_view');
            $this->load->view('user_menu/user_messages_menu_view');

            modules::load('user_notifications/notifications_controller')->renderNotificationsMenu(true);
        }
    }


    public function renderProfileMenu()
    {
        if ($this->MyUser->bLogged)
            $this->load->view('user_menu/user_profile_menu_view');
    }


}