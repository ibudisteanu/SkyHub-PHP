<?php

class CrawlerDataInfo
{
    public $sWebsite = 'http://welcome.ro/';
    public $arrWebsite = array('website.com');
    public $arrPages = array();

    public $sArticleInitialId;

    public $bRTrimSharp=true; // Remove all string after #

    public function checkURL($sURL)
    {
        foreach ($this->arrWebsite as $sData)
        {

            if ((strtolower($sData) == strtolower($sURL)) || (strtolower('www.'.$sData) == strtolower($sURL)))
                return true;
        }
        return false;
    }

    public $arrCrawlerExtraction = [];

    public function getCrawlerExtraction($sArrayElementName, $sArraySubElementName='')
    {
        $result = null;
        if (isset($this->arrCrawlerExtraction[$sArrayElementName])) {

            $result = $this->arrCrawlerExtraction[$sArrayElementName];

            if ($sArraySubElementName != '')
            {
                if (isset($result[$sArraySubElementName]))
                    $result = $result[$sArraySubElementName];
                else
                    $result = null;
            }
        }

        return $result;
    }

}