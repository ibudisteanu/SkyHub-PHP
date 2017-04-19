<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'modules/widgets/widgets_graphical/menu/main_menu/nav_menu/models/NavItem.php';
require_once APPPATH.'modules/widgets/widgets_graphical/ads/models/Display_ads_algorithm_model.php';

class Right_sidebar_Menu extends MY_Controller
{
    public $navMenu;

    public $bEnableAds=false;
    public $enAdsType =  TAdsType::adsSkyScrapper160_600;
    public $iSidebarWidth=0;
    public $bVisible=false;

    public function index($bEnableAds=true)
    {
        if ($this->MyUser->bLogged)
        {
            if (TUserRole::checkUserRights(TUserRole::Admin))
                $this->navMenu = $this->adminLoggedInMenu($bEnableAds);
            else
                $this->navMenu = $this->userLoggedInMenu($bEnableAds);
        } else
            $this->navMenu = $this->notLoggedInMenu($bEnableAds);
    }

    private function enableSideBarAds()
    {
        if (! modules::load('detect_useragent/detect_useragent')->isMobile()) {
            $this->bEnableAds = defined('ADS_ENABLED');
            $this->bVisible=true;
        }
    }

    protected function notLoggedInMenu($bEnableAds)
    {
        if ($bEnableAds) $this->enableSideBarAds();
    }

    protected function userLoggedInMenu($bEnableAds)
    {
        if ($bEnableAds) $this->enableSideBarAds();



    }

    protected function adminLoggedInMenu($bEnableAds)
    {
        if ($bEnableAds) $this->enableSideBarAds();
    }

    public function getContentWrapperStyle()
    {
        if (isset($this->iSidebarWidth))
            return 'margin-right: '.$this->iSidebarWidth.'px;';
    }

    public function renderMenu()
    {
        if ($this->bEnableAds)
        {
            if ($this->enAdsType == TAdsType::adsSkyScrapper160_600) $this->iSidebarWidth = 160;
            else $this->iSidebarWidth = 360;

            $this->iSidebarWidth +=1;

           $this->data['iWidth'] = $this->iSidebarWidth;

            $this->BottomScriptsContainer->addScript($this->load->view('right_sidebar/js/right_sidebar_scroll_bug_fix.js',null,true), true);
        } else
        {
            $this->iSidebarWidth=0;
            $this->data['iWidth'] = $this->iSidebarWidth;
        }

        if ($this->bVisible)
            $this->load->view('right_sidebar/right_sidebar_view', $this->data);
    }


}