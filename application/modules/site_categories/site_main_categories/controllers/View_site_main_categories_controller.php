<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class View_site_main_categories_controller extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        $this->load->model('site_main_categories/site_categories_model','SiteCategoriesModel');
    }

    public function index($sPage = 'TopCategoriesContainer')
    {
        switch ($sPage)
        {
            case 'TopCategoriesContainer':
                $this->getTopCategoriesView();
                break;
        }
    }

    private function getTopCategoriesView()
    {
        $this->data['dtSiteCategories']=$this->SiteCategoriesModel->findTopCategories();
        $this->ContentContainer->addObject($this->renderModuleView('site_categories_container_view',$this->data,TRUE),'',2);
    }

}