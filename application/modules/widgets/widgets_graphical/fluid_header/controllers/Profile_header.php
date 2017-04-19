<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Profile_header extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function index($UserObject=null)
    {
        if ($UserObject == null) {
            $UserObject = $this->MyUser;
            if (!$UserObject->bLogged) return false;
        }

        $HeaderToolBox = modules::load('toolbox/header_tool_box');
        $HeaderToolBox->createProfileMenu($UserObject);
        $this->data['HeaderToolBox'] = $HeaderToolBox;

        if (!$this->MyUser->bLogged)
            modules::load('auth_site/login')->index('box_header');

        $this->getHeaderContainerView($UserObject);
    }

    protected function getHeaderContainerView($UserObject)
    {
        $this->load->vars(array('g_User' => $UserObject));
        $this->ContentContainer->addObject($this->renderModuleView('profile_header_view',$this->data,TRUE),'',1);
    }



}