<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Forum_header extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function index($Forum)
    {
        $this->data['ForumObject'] = $Forum;
        $HeaderToolBox = modules::load('toolbox/header_tool_box');
        $HeaderToolBox->createForumMenu($Forum);
        $this->data['HeaderToolBox'] = $HeaderToolBox;

        if (!$this->MyUser->bLogged)
            modules::load('auth_site/login')->index('box','style="margin-top:20px;  max-width: 420px; float: right;" class="col-lg-md-2 col-md-4 col-sm-5 hidden-xs hidden-xxs hidden-tn"');

        $this->getHeaderContainerView();
    }

    function getHeaderContainerView()
    {
        $this->ContentContainer->addObject($this->renderModuleView('forum_header_view',$this->data,TRUE),'',1);
    }



}