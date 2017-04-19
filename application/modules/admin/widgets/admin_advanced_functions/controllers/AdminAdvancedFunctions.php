<?php

class AdminAdvancedFunctions extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        $this->ContentContainer->addObject($this->renderModuleView('admin_advanced_functions_view',$this->data, TRUE),'<section class="col-lg-7 connectedSortable">');
    }

}