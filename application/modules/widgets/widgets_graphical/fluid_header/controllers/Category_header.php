<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Category_header extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function index($Category)
    {

        $this->data['CategoryObject'] = $Category;
        $HeaderToolBox = modules::load('toolbox/header_tool_box');

        if ($Category->sParentId==null)
            $HeaderToolBox->createSiteCategoryMenu($Category);
        else
            $HeaderToolBox->createSiteSubCategoryMenu($Category);

        $this->data['HeaderToolBox'] = $HeaderToolBox;

        if (!$this->MyUser->bLogged)
            modules::load('auth_site/login')->index('box_header');

        $this->getHeaderContainerView();
    }

    function getHeaderContainerView()
    {
        $this->ContentContainer->addObject($this->renderModuleView('category_header_view',$this->data,TRUE),'',1);
    }



}