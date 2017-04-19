<?php

class Emails_board extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function index($sActionName='', $sID='')
    {
        if (TUserRole::checkUserRights(TUserRole::Admin))
        {
            $this->renderPage($sActionName, $sID);
        } else
        {
            //redirect('/login/form/', 'refresh');
            show_404();
        }
    }


    public function renderPage($sActionName, $sID)
    {
        $this->ContentContainer->addObject($this->renderModuleView('dashboard',null,true));

        $this->Template->loadMeta('Emails Board','Send emails using '.WEBSITE_TITLE);
        $this->Template->renderHeader('Emails board');
        modules::load('send_emails/send_emails')->index($sActionName, $sID);

        $this->Template->renderContainer();
        $this->Template->renderFooter();
    }
}