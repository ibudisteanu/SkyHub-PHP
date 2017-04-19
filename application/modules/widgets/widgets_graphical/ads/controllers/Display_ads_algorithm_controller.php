<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Display_ads_algorithm_controller extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        $this->load->model('ads/Display_ads_algorithm_model','DisplayAdsAlgorithmModel');
    }

    public function initializeAdsAlgorithm($iAlgorithmSteps)
    {
        $this->DisplayAdsAlgorithmModel->initializeAlgorithm($iAlgorithmSteps);
    }

    public function renderCommentAds($bHidden=true)
    {
        $AdToDisplay = $this->DisplayAdsAlgorithmModel->checkIfDisplayAds();
        if ($AdToDisplay != null)
            return $this->renderDisplayAd($AdToDisplay->enAdsType, $bHidden);

        return '';
    }

    public function renderRightSidebarAds($bHidden=true)
    {
        $AdToDisplay = $this->DisplayAdsAlgorithmModel->getForcedAds(TAdsType::adsSkyScrapper160_600);
        if ($AdToDisplay != null)
            return $this->renderDisplayAd($AdToDisplay, $bHidden);

        return '';
    }

    protected function renderDisplayAd($enAdToDisplay, $bHidden=true)
    {
        switch ($enAdToDisplay)
        {
            case TAdsType::ads728by90:
                return $this->renderModuleView('display_ad_728_90_view',$this->data,$bHidden);

            case TAdsType::adsResponsive:
                return $this->renderModuleView('display_ad_responsive_view',$this->data,$bHidden);

            case TAdsType::adsSkyScrapper160_600:
                return $this->renderModuleView('display_ad_sky_scrapper_160_600_view',$this->data,$bHidden);

            case TAdsType::adsSkyScrapper300_600:
                return $this->renderModuleView('display_ad_sky_scrapper_300_600_view',$this->data,$bHidden);

        }
    }

}