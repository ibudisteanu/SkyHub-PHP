<?php

class Dashboard extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function index($sParam1='', $sParam2='', $sParam3='')
    {
        if (TUserRole::checkUserRights(TUserRole::Admin))
        {
            $this->showHome($sParam1, $sParam2, $sParam3='');
        } else
        {
            //redirect('/login/form/', 'refresh');
            $this->AlertsContainer->addAlert('g_msgGeneralError','error','Not enough rights for updating');
            return;
        }
    }

    public function showHome($sParam1, $sParam2, $sParam3='')
    {

        $this->Template->loadMeta('Admin Dashboard','Admin Dashboard for the '.WEBSITE_TITLE);
        $this->Template->renderHeader('admin');
        $this->ContentContainer->addObject($this->renderModuleView('dashboard',$this->data,true));

        modules::load('widget_others/StatBox')->index();

        modules::load('admin_advanced_functions/AdminAdvancedFunctions')->index($sParam1, $sParam2,$sParam3);
        modules::load('admin_cache/AdminCache')->index($sParam1, $sParam2, $sParam3);
        modules::load('db/Db_app')->index($sParam1, $sParam2, $sParam3);

        modules::load('widget_others/StatUsers')->index();

        modules::load('widget_others/MapVisitors')->index();

        modules::load('widget_others/ToDoList')->index();

        $this->Template->renderContainer();
        $this->Template->renderFooter();
    }
}