<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class NavItem
{
    public $sText;
    public $sLink;
    public $arrSubItems;
    public $sOnClick;
    public $sImg;
    public $iLevel;
    public $bHeader;

    public $sLabelAttachedValue;
    public $sLabelAttachedType;

    function NavItem($sText='', $sLink='', $sImg='', $arrSubItems=[], $iLevel=0,$bHeader=false,$sLabelAttachedType='',$sLabelAttachedValue='',$sOnClick='')
    {
        $this->sText = $sText;
        $this->sLink = $sLink;
        $this->arrSubItems = $arrSubItems;
        $this->sImg = $sImg;
        $this->iLevel = $iLevel;
        $this->bHeader = $bHeader;
        $this->sLabelAttachedValue=$sLabelAttachedValue;
        $this->sLabelAttachedType=$sLabelAttachedType;

        $this->sOnClick = $sOnClick;
    }

}