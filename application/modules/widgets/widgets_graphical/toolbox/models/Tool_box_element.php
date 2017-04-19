<?php

class Tool_box_element
{
    public $sGroupName;
    public $sName;
    public $sText;
    public $sURL;
    public $sImage;
    public $bVisible;
    public $sOnClick;
    public $sObjectID;

    private $sColor;

    public function __construct($sGroupName, $sName, $sText, $sImage, $sURL, $sColor, $bVisible, $sObjectID, $sOnClick )
    {

        $this->sGroupName = $sGroupName;
        $this->sName = $sName;
        $this->sText = $sText;
        $this->sURL = $sURL;
        $this->sImage = $sImage;
        $this->bVisible = $bVisible;
        $this->sOnClick = $sOnClick;
        $this->sObjectID = $sObjectID;
        $this->sColor = $sColor;
    }

    public function getColor()
    {
        switch ($this->sColor)
        {
            case 'primary': return '#ffd700'; //gold
            case 'secondary': return '#ff7f50'; //orange
            case 'tertiary': return'#87cefa'; //aqua
        }

        return $this->sColor;
    }

}