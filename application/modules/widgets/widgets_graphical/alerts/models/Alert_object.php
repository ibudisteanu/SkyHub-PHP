<?php

//Alert Object
class Alert_object
{
    public $sName;
    public $sNameInitial;
    public $sHeader;
    public $sType;
    public $sTypeClass;
    public $sText;
    public $sIcon;
    public $bDismissible;

    public function __construct($sName, $sType, $sText, $sHeader, $bDismissible, $sIcon)
    {
        $this->sName = $sName;
        $this->sNameInitial = $sName;
        $this->sText = $sText;
        $this->bDismissible = $bDismissible;
        $this->sType = $sType;

        if ($sIcon == 'default')
        {
            switch ($sType)
            {
                case 'error':
                    $sIcon = 'fa fa-ban';
                    $this->sTypeClass = '-danger';
                    if ($sHeader == 'default') $sHeader = 'Error';
                    break;
                case 'warning':
                    $sIcon = 'fa fa-warning';
                    $this->sTypeClass = '-warning';
                    if ($sHeader == 'default') $sHeader = 'Warning';
                    break;
                case 'info':
                    $sIcon = 'fa fa-info';
                    $this->sTypeClass = '-info';
                    if ($sHeader == 'default') $sHeader = 'Info';
                    break;
                case 'success':
                    $sIcon = 'fa fa-check';
                    $this->sTypeClass = '-success';
                    if ($sHeader == 'default') $sHeader = 'Success';
                    break;
            }
        }

        $this->sHeader = $sHeader;
        $this->sIcon = $sIcon;
    }



}