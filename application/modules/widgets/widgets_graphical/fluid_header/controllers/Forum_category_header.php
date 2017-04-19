<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Forum_category_header extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function index($forumCategoryObject)
    {
        $this->data['forumCategoryObject'] = $forumCategoryObject;
        $HeaderToolBox = modules::load('toolbox/header_tool_box');

        $HeaderToolBox->createForumCategoryMenu($forumCategoryObject);
        /*
        if ($forumCategoryObject->Parent==null)
            $HeaderToolBox->createForumCategoryMenu($forumCategoryObject);
        else
            $HeaderToolBox->createSiteSubCategoryMenu($forumCategoryObject);*/

        $this->data['HeaderToolBox'] = $HeaderToolBox;

        if (!$this->MyUser->bLogged)
            modules::load('auth_site/login')->index('box','style="margin-top:20px;  max-width: 420px; float: right;" class="col-lg-md-2 col-md-4 col-sm-5 hidden-xs hidden-xxs hidden-tn"');

        $this->getHeaderContainerView();
    }

    function getHeaderContainerView()
    {
        $this->ContentContainer->addObject($this->renderModuleView('forum_category_header_view',$this->data,TRUE),'',1);
    }

}