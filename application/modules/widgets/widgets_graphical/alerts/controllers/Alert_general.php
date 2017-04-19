<?php

class Alert_general extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

    }

    public function index()
    {
        $this->renderView();
    }

    private function renderView()
    {
        //Index Value = 1
        $this->ContentContainer->addObject($this->renderModuleView('alert_general_view',$this->data, TRUE),'',5);
    }


}