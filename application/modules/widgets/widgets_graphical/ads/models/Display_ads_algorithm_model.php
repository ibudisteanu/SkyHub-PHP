<?php

abstract class TAdsType
{
    const ads728by90 = 0;
    const adsResponsive = 1;
    const ads336by90 = 2;
    const adsSkyScrapper300_600 = 3;
    const adsSkyScrapper160_600 = 4;
}

class TAdsToDisplay
{
    public $iStepIndexToDisplay;
    public $bDisplayed;
    public $enAdsType;

    function __construct($iIndex, $bDisplayed, $enAdsType)
    {
        $this->iStepIndexToDisplay=$iIndex;
        $this->bDisplayed = $bDisplayed;
        $this->enAdsType = $enAdsType;
    }
}

class Display_ads_algorithm_model extends MY_Model
{
    public $iAdsCounter;//how many ads I already displayed
    public $iAdsDisplayed=0;
    public $iAlgorithmStepIndex;

    //Maximum 3 Ads per page
    //public $vAdsType = array(TAdsType::adsResponsive,TAdsType::adsResponsive, TAdsType::adsResponsive);
    public $vAdsType = array(TAdsType::ads728by90,TAdsType::ads728by90, TAdsType::ads728by90);

    public $arrAdsToDisplay = array();

    function __construct()
    {
        parent::__construct();
        $this->iAdsDisplayed = 0;
        //$this->initDB('Statistics',TUserRole::notLogged,TUserRole::notLogged,TUserRole::notLogged,TUserRole::SuperAdmin);
    }

    protected  function addToDisplay($iStepIndexToDisplay)
    {
        $obj = new TAdsToDisplay($iStepIndexToDisplay,false,$this->vAdsType[count($this->arrAdsToDisplay)]);
        array_push($this->arrAdsToDisplay, $obj);
    }

    public function initializeAlgorithm($iAlgorithmSteps)
    {
        $this->iAdsCounter=0;
        if ($iAlgorithmSteps < 5)//too few, no ads
        {
        } else
        if ($iAlgorithmSteps < 10)
        {//Display at the end one
            $this->addToDisplay($iAlgorithmSteps);
        } else
        if ($iAlgorithmSteps < 30)
        {
            $this->addToDisplay((int)($iAlgorithmSteps / 2 + rand(0,5)));
            $this->addToDisplay($iAlgorithmSteps);
        }
        else
        {
            $this->addToDisplay((int)($iAlgorithmSteps / 3 + rand(0,5)));
            $this->addToDisplay((int)(2*$iAlgorithmSteps / 3 + rand(0,5)));
            $this->addToDisplay($iAlgorithmSteps);
        }

        $this->checkAlgorithmValidation();
    }

    protected function checkAlgorithmValidation()
    {
        //check if the ads are soo close
        for ($index=0; $index < count($this->vAdsType)-1; $index++)
        {
            if ($index+1 >= count($this->vAdsType)) break;
            $Ad1 = $this->vAdsType[$index];
            $Ad2 = $this->vAdsType[$index+1];
            if (($Ad1!=null)&&($Ad2!=null)&&(abs($Ad1->iStepIndexToDisplay - $Ad2->iStepIndexToDisplay) < 4))//if there are not at least 3-4 comments between the two ads then I will remove one
            {
                //unset($this->vAdsType[$index+1]); delete OR
                $this->vAdsType[$index+1]->iStepIndexToDisplay += 3 +rand(0,2);
                $index--;
            }
        }
    }

    public function checkIfDisplayAds()
    {
        $this->iAlgorithmStepIndex++;
        foreach ($this->arrAdsToDisplay as $Ad)
        {
            if ($Ad->iStepIndexToDisplay == $this->iAlgorithmStepIndex)
            {
                return $Ad;
            }
        }
        return null;
    }

    public function getForcedAds($ads = TAdsType::adsSkyScrapper160_600)
    {
        $this->iAdsCounter++;
        return $ads;
    }

}