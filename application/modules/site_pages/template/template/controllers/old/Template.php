<?php


class Template extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->Header= modules::load('Template/Header');
    }

    public function renderNav()
    {
        modules::run('main_menu/nav_menu/index', NULL);
        $this->renderModuleView('nav');
    }

    public function renderHeader($sTitle, $sActivePage)
    {
        $this->load->vars(array('g_sTitle' => $sTitle));
        $this->load->vars(array('g_sActivePage' => $sActivePage));

        $this->renderModuleView('header', $this->data);

        $this->Header->index();

        $this->renderNav();
    }

    public function renderFooter()
    {
        modules::run('main_menu/footer_menu/index', NULL);
        $this->renderModuleView('footer');
    }

    public function renderContainer()
    {
        $this->renderModuleView('container');
    }

}