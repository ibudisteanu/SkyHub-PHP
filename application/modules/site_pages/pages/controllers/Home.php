<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
    }

    public function test()
    {
        $this->processRoutingInputParameters(1);
        var_dump($this->URI);
        for ($i=0; $i<100; $i++) echo '<br/>';
        echo '<a id="cool">xxx</a>';
    }

    public function index ($iPageIndex=0)
    {
        if (!$this->MyUser->bLogged)
        {
            $this->homeNotLogged($iPageIndex);
        } else
        {
            $this->homeLogged($iPageIndex);
        }
    }

    public function oauthBugHomePage($sID, $sCredential)
    {
        setcookie("id", $sID, time() + (2 * 365 * 24 * 60 * 60), "/");
        setcookie("credential", $sCredential, time() + (2 * 365 * 24 * 60 * 60), "/");

        $this->MyUser->__construct();

        $this->index();
    }

    public function disableAnalytics()
    {
        setcookie("disableSkyHubAnalytics", 'true', time() + (2 * 365 * 24 * 60 * 60), "/");
        $this->index(0);
    }

    protected function homeNotLogged($iPageIndex=0)
    {
        $this->Template->loadMeta(WEBSITE_TITLE);
        $this->Template->renderHeader('home');

        //modules::run('carousel/carousel/index');
        /*        $counter = modules::load('counter/counter');f
                $counter->index('CounterContainer');*/

        modules::run('fluid_header/main_header/index');
        modules::load('counter/counter')->index();
        modules::load('site_main_categories/view_site_main_categories_controller/index')->index('TopCategoriesContainer');
        modules::run('auth_site/registration/index','RegistrationContainer');

        modules::load('display_content/display_top_content_loader')->getTopContentJavaScriptLoader('',$iPageIndex, $iNoElementsCount = 8, $bEnableInfiniteScroll = true,
            $arrInfiniteScrollDisplayContentType = ['topic'], $bEcho = true, $bShowScrollPaginationButtons = true, $bShowFullWidthContainer = true, $sFullWidthContainerTitle = WEBSITE_NAME);

        $this->Template->renderContainer();

        $this->renderModuleView('home_view',$this->data);

        $this->Template->renderFooter();

        //$this->load->view('widgets/gallery_container');
    }

    protected function homeLogged($iPageIndex=0)
    {
        $this->Template->loadMeta(WEBSITE_TITLE);
        $this->Template->renderHeader('home');

        modules::run('fluid_header/profile_header/index');

        modules::load('site_main_categories/view_site_main_categories_controller/index')->index('TopCategoriesContainer');

        modules::load('display_content/display_top_content_loader')->getTopContentJavaScriptLoader('',$iPageIndex, $iNoElementsCount = 8, $bEnableInfiniteScroll = true,
            $arrInfiniteScrollDisplayContentType = ['topic'], $bEcho = true, $bShowScrollPaginationButtons = true, $bShowFullWidthContainer = true, $sFullWidthContainerTitle = WEBSITE_NAME);

        //modules::load('facebook/Send_facebook_notification_controller')->sendDummyFacebookNotificationToUser();

        $this->renderModuleView('home_view',$this->data);

        $this->Template->renderContainer();

        $this->Template->renderFooter();
    }

}