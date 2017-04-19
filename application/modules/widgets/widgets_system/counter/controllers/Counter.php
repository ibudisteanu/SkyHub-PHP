<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Counter extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        $this->load->model('counter/counter_statistics','CounterStatistics');

        $this->data['counterUsers']=$this->CounterStatistics->iUsersCount;
        $this->data['counterForums']=$this->CounterStatistics->iForumsCount;
        $this->data['counterComments']=$this->CounterStatistics->iCommentsCount;
        $this->data['counterTopics']=$this->CounterStatistics->iTopicsCount;
    }

    public function index($sPage = 'CounterContainer')
    {
        switch ($sPage)
        {
            case 'CounterContainer':
                $this->getCounterContainerView();
                break;
        }
    }

    function getCounterContainerView()
    {
        //$this->ContentContainer->addObject($this->renderModuleView('counter_container',$this->data,TRUE),'',2);
        $this->ContentContainer->addObject($this->load->view('counter/counter_container',$this->data,TRUE),'',2);
    }

}