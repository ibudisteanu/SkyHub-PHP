<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Topic_header extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function index($Topic)
    {
        $this->data['TopicObject'] = $Topic;
        $HeaderToolBox = modules::load('toolbox/header_tool_box');
        $HeaderToolBox->createTopicMenu($Topic);
        $this->data['HeaderToolBox'] = $HeaderToolBox;

        if (!$this->MyUser->bLogged)
            modules::load('auth_site/login')->index('box','style="margin-top:20px;  max-width: 420px; float: right;" class="col-lg-md-2 col-md-4 col-sm-5 hidden-xs hidden-xxs hidden-tn"');

        $this->getHeaderContainerView();
    }

    function getHeaderContainerView()
    {
        $this->ContentContainer->addObject($this->renderModuleView('topic_header_view',$this->data,TRUE),'',1);
    }



}