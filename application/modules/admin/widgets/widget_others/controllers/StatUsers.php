<?php

class StatUsers extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        $this->ContentContainer->addObject($this->renderModuleView('statusers',$this->data, TRUE),'<section class="col-lg-7 connectedSortable ui-sortable">');
    }

}