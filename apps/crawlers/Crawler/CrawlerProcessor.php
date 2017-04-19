<?php

require_once __DIR__.'/CrawlerStrings.php';
require_once __DIR__.'/CrawlerProcessorBasic.php';
require_once __DIR__.'/CrawlerVisitedSites.php';
require_once __DIR__.'/CrawlerTags.php';
require_once __DIR__.'/../../helpers/users/User.php';

class CrawlerProcessor extends CrawlerProcessorBasic
{
    protected $arrCrawlerDataInformation = [];

    protected  $timeLastJob = null;
    protected  $arrJobsResults = [];

    protected $arrPagesQueue = [];

    public function __construct()
    {
        parent::__construct();

        $this->CrawlerVisitedSites->saveFile();
    }

    public function setCrawlerDataInfo($arrCrawlerDataInformation)
    {
        $this->arrCrawlerDataInformation = $arrCrawlerDataInformation;
    }

    public function startCrawl($iDepth=4)
    {
        ob_implicit_flush(true); ob_start();

        if ($this->arrCrawlerDataInformation == []){
            echo 'No crawler info';
            return;
        }

        $this->iPagesCrawled=0;

        $this->arrPagesQueue = [];
        foreach ($this->arrCrawlerDataInformation as $CrawlerDataInformation)
        {
            $this->CrawlerStrings->CrawlerInfo = $CrawlerDataInformation;

            foreach ($CrawlerDataInformation->arrPages as $Page)
            {
                $this->addToCrawlQueue($CrawlerDataInformation, $Page['link'], $iDepth);
            }
        }

        $this->processCrawlerQueue();

        echo '<br/>';
        for ($index=0; $index < 20; $index++)
            echo '**************************************<br/>';
        
        $this->jobFinished();
        echo 'Number pages: '.$this->iPagesCrawled;

        ob_end_flush();
    }

    public function addToCrawlQueue($CrawlerDataInformation, $sLink, $iDepth=4)
    {
        array_push($this->arrPagesQueue,['CrawlerDataInformation'=>$CrawlerDataInformation,'link'=>$sLink, 'depth'=>$iDepth]);
    }

    public function processCrawlerQueue()
    {
        for ($i=0; $i<count($this->arrPagesQueue); $i++)
        {
            $queueElement = $this->arrPagesQueue[$i];

            $this->crawlPage($queueElement['CrawlerDataInformation'],$queueElement['link'],$queueElement['depth']);
        }
    }

    public function crawlPage($CrawlerDataInformation, $sUrl, $depth = 5)
    {
        static $seen = array();
        if (isset($seen[$sUrl]) || $depth === 0)
            return;

        $seen[$sUrl] = true; $seen[preg_replace("/^http:/i", "https:", $sUrl)] = true; $seen[preg_replace("/^https:/i", "http:", $sUrl)] = true;
        $sInitialUrl = $sUrl;

        if ($CrawlerDataInformation->bRTrimSharp)
            if (strpos($sUrl, "#") !== false)
                $sUrl = substr($sUrl, 0, strpos($sUrl, "#"));

        foreach ($CrawlerDataInformation->arrUnFollowPages as $unFollowPage) {

            if ((isset($unFollowPage['remove']))&&(strpos($sUrl, $unFollowPage['remove']) !== false))
                $sUrl = str_replace($unFollowPage['remove'], '', $sUrl);

            if ((isset($unFollowPage['contains']))&&(strpos($sUrl, $unFollowPage['contains']) !== false)) return;
            if ((isset($unFollowPage['link']))&&($sUrl == $unFollowPage['link'])) return;
        }

        if ($sInitialUrl != $sUrl) {
            if (isset($seen[$sUrl]) || $depth === 0) {
                var_dump($sInitialUrl);
                var_dump($sUrl);
                return;
            }
            $seen[$sUrl] = true; $seen[preg_replace("/^http:/i", "https:", $sUrl)] = true; $seen[preg_replace("/^https:/i", "http:", $sUrl)] = true;
        }

        $urlData = parse_url($sUrl);
        if (!(($urlData['scheme'] == 'http') || ($urlData['scheme'] == 'https'))) return null;

        if (!$CrawlerDataInformation->checkURL($urlData['host'])) return null;

        $this->iPagesCrawled ++ ;

        $dom = new DOMDocument('1.0');
        @$dom->loadHTMLFile($sUrl);

        echo("URL: " . $sUrl . PHP_EOL . "<br/>");

        ob_flush();

        $this->processNewsDOM($CrawlerDataInformation, $sUrl, $dom);
        //echo PHP_EOL . PHP_EOL;

        $h1 = $dom->getElementsByTagName('h1');
        foreach ($h1 as $element)
            if (($element != null) && ($element->nodeValue == '404 Page Not Found')) {
                echo "<b>404 Page Not Found rejected </b>".$sUrl.' <br/>';
                return;
            }

        $anchors = $dom->getElementsByTagName('a');
        foreach ($anchors as $element)
            if ($element != null)
            {
                $href = $element->getAttribute('href');
                if (0 !== strpos($href, 'http')) {
                    $path = '/' . ltrim($href, '/');
                    if (extension_loaded('http')) {
                        $href = http_build_url($sUrl, array('path' => $path));
                    } else {
                        $parts = parse_url($sUrl);
                        $href = $parts['scheme'] . '://';
                        if (isset($parts['user']) && isset($parts['pass'])) {
                            $href .= $parts['user'] . ':' . $parts['pass'] . '@';
                        }
                        $href .= $parts['host'];
                        if (isset($parts['port'])) {
                            $href .= ':' . $parts['port'];
                        }
                        $href .= $path;
                    }
                }
                $this->addToCrawlQueue($CrawlerDataInformation, $href, $depth-1);
            }
        //echo "URL:",$sUrl,PHP_EOL,"CONTENT:",PHP_EOL,$dom->saveHTML(),PHP_EOL,PHP_EOL;

    }

    protected function processNewsDOM($CrawlerDataInformation, $url, $DOM)
    {

    }

    protected function addJob($array)
    {
        if ($array != null)
            array_push($this->arrJobsResults, $array);

        $min = 0;
        if ($this->timeLastJob == null)
            $this->timeLastJob = new DateTime();
        else {
            $time = new DateTime();
            $diff = $time->diff($this->timeLastJob);
            $min = $diff->i + $diff->h * 60 + $diff->d * 60 * 24 + $diff->m * 60 * 24 * 30 + $diff->y * 60 * 24 * 30 * 365;
        }

        if ((count($this->arrJobsResults) >= 7) || ($min > 10)) {

            if (count($this->arrJobsResults) > 0)
                $this->jobFinished();

            $this->timeLastJob = new DateTime();
        }

        if ($array != null) {
            echo("<b>-----------------------------------------------------------------------------------------------------------------</b><br/>");
            echo("<b>Success Job added " . $array['url original'] . " time " . $array['iIntervalMin'] . " TIME_DIFF_MIN " . $min . "</b><br/>");
            echo("<b>-----------------------------------------------------------------------------------------------------------------</b><br/>");
        }
    }

    protected  function jobFinished()
    {

    }

}