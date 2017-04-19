<?php

class StatBox extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        $this->ContentContainer->addObject($this->renderModuleView('statbox',$this->data, TRUE),'');
    }

}