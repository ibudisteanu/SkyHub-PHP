<?php


class Bottom_script_object
{
    public $sText;
    public $sScriptName;
    public $bPrintedAlready;

    public function __construct($sText, $sScriptName)
    {
        $this->sText=$sText;
        $this->sScriptName = $sScriptName;
        $this->bPrintedAlready = false;
    }

    public function printObject()
    {
        $this->bPrintedAlready = true;
        if (strlen($this->sText) > 0)
            echo $this->sText;
    }

}