<?php

//http://codepen.io/eMaj/pen/qtico
//http://bootsnipp.com/snippets/featured/triangle-breadcrumbs-arrows

class Breadcrumb extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('StringsAdvanced',null,'StringsAdvanced');
    }

    public function addBreadcrumbObject($arrBreadCrumb,$indexValue=2)
    {
        $this->ContentContainer->addObject($this->getBreadcrumb($arrBreadCrumb),'',$indexValue);
    }

    public function getBreadcrumb($arrBreadCrumb)
    {
        $data['arrBreadCrumb'] = $arrBreadCrumb;
        return $this->renderModuleView('breadcrumb_view',$data,TRUE);
    }

    public function renderBreadcrumb($arrBreadCrumb)
    {
        echo $this->getBreadcrumb($arrBreadCrumb);
    }

}