<?php

class Popup_authentication extends MY_Controller
{
    public $bAlreadyIncluded=false;

    public  function __construct()
    {
        parent::__construct();
    }

    public function test_index()
    {
        $this->Template->loadMeta(WEBSITE_TITLE);
        $this->Template->renderHeader('home');


        /*modules::run('fluid_header/main_header/index');
        modules::load('auth_site/login')->index('page');
        modules::run('auth_site/registration/index','RegistrationContainer');*/

        $sContent = $this->renderModuleView('popup_authentication_index_view',null,TRUE);
        $this->ContentContainer->addObject($sContent,'<div style="padding-top: 200px">',3);

        $this->loadRequirements();

        $this->Template->renderContainer();
        $this->Template->renderFooter();
    }

    public function loadRequirements()
    {
        if ($this->bAlreadyIncluded)
            return;

        $this->BottomScriptsContainer->addScript('<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery.leanmodal/1.1/jquery.leanmodal.min.js"></script>');
        $this->BottomScriptsContainer->addScript('<link rel="stylesheet" type="text/css" href="'.base_url('assets/popup/auth/popup_auth.css').'">');
        $this->ContentContainer->addObject($this->renderModuleView('popup_authentication_view.php',null,TRUE),'',3);
        //$this->BottomScriptsContainer->addScript();


        $this->BottomScriptsContainer->addScriptResFile(base_url( defined('WEBSITE_OFFLINE') ? "app/res/js/login-popup-authentication.js" : 'assets/min-js/login-popup-authentication-min.js'));

        $this->bAlreadyIncluded=true;
    }

}