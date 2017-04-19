<?php

class About extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
    }

    function index ()
    {
        $this->Template->loadMeta('About '.WEBSITE_TITLE);
        $this->Template->renderHeader('about');

        modules::run('fluid_header/main_header/index');

        $this->ContentContainer->addObject($this->renderModuleView('about_view',$this->data,true));

        $this->Contact = modules::load('contact/contact');
        $this->Contact->index();

        $this->Template->renderContainer();

        $this->Template->renderFooter();
    }

}