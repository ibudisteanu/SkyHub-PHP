<?php

class MapVisitors extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        $this->ContentContainer->addObject($this->renderModuleView('mapvisitors',$this->data, TRUE),'<section class="col-lg-5 connectedSortable">');
        //$this->addContainer($this->renderModuleView('views/mapvisitors',$this->data, TRUE));
    }

}