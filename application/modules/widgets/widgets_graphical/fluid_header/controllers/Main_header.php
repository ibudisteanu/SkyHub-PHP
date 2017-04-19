<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Main_header extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $HeaderToolBox = modules::load('toolbox/header_tool_box');
        $HeaderToolBox->createMainMenu(null);
        $this->data['HeaderToolBox'] = $HeaderToolBox;

        if (!$this->MyUser->bLogged) {
            modules::load('auth_site/login')->index('box', 'style="margin-top:20px;  max-width: 420px" class="col-md-4 col-md-offset-0 col-sm-5 col-sm-offset-0 col-xs-6 col-xs-offset-0 col-xxs-12 col-xxs-offset-0 col-tn-12 col-tn-offset-0" ');
        }

        $this->getHeaderContainerView();
    }

    function getHeaderContainerView()
    {
        $this->ContentContainer->addObject($this->renderModuleView('main_header_view',$this->data,TRUE),'',1);
    }



}