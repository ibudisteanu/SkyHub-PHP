<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Carousel extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function index($sPage = 'CounterContainer')
    {
        $this->getCarouselContainerView();
    }

    function getCarouselContainerView()
    {
        $this->ContentContainer->addObject($this->renderModuleView('carousel',$this->data,TRUE));
    }

}