<?php


class Content_object
{
    public $bChecked=false;
    public $sHeader;

    public $sBody;
    public $iID;

    public $sEnd;

    public function __construct($sHeader, $sBody, $iID, $sEnd)
    {
        $this->bChecked=false;

        $this->sHeader=$sHeader;
        $this->sBody = $sBody;
        $this->iID = $iID;
        $this->sEnd = $sEnd;
    }

    public function printObject()
    {
        if (strlen($this->sHeader)>0)
            echo $this->sHeader;

        echo $this->sBody;

        if (strlen($this->sEnd)>0)
            echo $this->sEnd;
    }

}