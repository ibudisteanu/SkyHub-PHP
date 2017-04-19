<?php

require_once __DIR__ . '/../CrawlerProcessor.php';
require_once __DIR__ . '/../../crawlers_info/news/Default_info.php';
require_once __DIR__ . '/../../../helpers/skyhub_server/server_uri.php';

class CrawlerNews extends CrawlerProcessor
{
    public $CrawlerDataInformationDefault;
    public function __construct()
    {
        parent::__construct();

        $this->CrawlerDataInformationDefault = new CrawlerData_Default_info();

        echo 'Creating Crawler Instance ... <br/>';
    }

    //TEMPORARY USED DOM NODEs
    protected $nodeTitle, $nodeContent, $nodeAuthor, $nodeDate, $nodeTime, $nodeShortDescription,
              $nodeImage, $nodeImageAlt, $nodeTags, $nodeLanguage;

    //EXTRACTED DATA
    protected $sTitle, $sContent, $sAuthor, $sDate, $sTime, $sImage, $sImageAlt, $arrTags, $sShortDescription, $sLanguage;

    protected function processNewsDOM($CrawlerDataInformation,  $url, $DOM)
    {
        $this->initializeDataFound();
        $this->initializeNodes();
        $this->extractDefaultCrawledData($DOM);

        $nodeContainer = $this->checkElement($DOM, array($DOM), $CrawlerDataInformation->getCrawlerExtraction('container'));

        if ($nodeContainer != null)
        {
            $bPrintAggregatedData = false;

            if (!is_array($nodeContainer)) $nodeContainer = array($nodeContainer);

            $this->extractPersonalizedCrawledData($CrawlerDataInformation, $DOM, $nodeContainer);

            /*FIXING DATE TIME */
            $this->CrawlerStrings->fixDateTime($this->sDate);
            $this->CrawlerStrings->fixDateTime($this->sTime);

            $this->CrawlerStrings->replaceDates($this->sDate);

            /*FIXING OTHER CONTENT */

            $this->sTitle = $this->CrawlerStrings->processOnlyText($this->sTitle);
            $this->sContent = $this->CrawlerStrings->processOnlyText($this->sContent,'xss|whitespaces');
            $this->sAuthor = $this->CrawlerStrings->processOnlyText($this->sAuthor);

            $this->sDate = $this->CrawlerStrings->processOnlyText($this->sDate);
            $this->sTime = $this->CrawlerStrings->processOnlyText($this->sTime);

            $this->sDate = $this->CrawlerStrings->getDateRegexString($this->CrawlerStrings->processOnlyText($this->sDate));
            $this->sTime = $this->CrawlerStrings->getTimeRegexString($this->CrawlerStrings->processOnlyText($this->sTime));

            $this->CrawlerStrings->repairURL($this->sImage);

            for ($i=0; $i<count($this->arrTags); $i++)
                $this->arrTags[$i] = $this->CrawlerStrings->processOnlyText($this->arrTags[$i]);


            $bPrintAggregatedData = true;

            ///$dateTime = strtotime($sTime);

            $today = new DateTime();
            try {
                $dateTime = new DateTime($this->sDate.' '.$this->sTime);
            } catch (Exception $e) {
                $dateTime = new DateTime();
                echo '<br/><br/>Date Error: ',  $e->getMessage(), "\n<br/><br/>";
            }
            $interval = $dateTime->diff($today);
            $iIntervalDays = $interval->days + $interval->y * 365 + $interval->m*30;
            $iIntervalMin = $iIntervalDays * 24*60 + $interval->h*60 +  $interval->m;
            $bJobAdded=false;

            if (($url != '') && ($this->sTitle != '') && ($this->sContent != '') && ($iIntervalMin > 0) && (count($this->arrTags) >= 0))
            {
                if ($iIntervalDays >= 3)
                {
                    echo("<b>Rejected for days: ".$iIntervalDays.' '.$url." </b><br/>");
                    $bPrintAggregatedData=true;
                } else
                    if ($this->CrawlerVisitedSites->checkUploadedSite($url, $this->sTitle) != -1)
                    {
                        echo("<b>Rejected for uploaded already: s".$url." </b><br/>");
                        $bPrintAggregatedData=true;
                    }
                    else
                    {
                        $crawlerIdentifiedCategory = $this->CrawlerTags->identifyCategoryToPublishFromProcessTags($this->arrTags);
                        if ($crawlerIdentifiedCategory != null)
                        {
                            $sTagsArray = '';
                            foreach ($this->arrTags as $sTag)
                                $sTagsArray .= $sTag.' , ';

                            $sContentNew = $this->sContent.'<br/><br/>';
                            $sContentNew .= $this->sDate.' '.$this->sTime.'   '.$this->sAuthor.'   <br/>';
                            $sContentNew .= '<a href="'.$CrawlerDataInformation->sWebsite.'">'.$CrawlerDataInformation->sWebsite.'</a> <br/>';

                            $additionalInformation = [
                                    'scraped'=>true,
                                    'originalURL'=>$url,
                                    'website'=>$CrawlerDataInformation->sWebsite,
                                    'websiteName'=>$CrawlerDataInformation->sWebsiteName,
                                ];

                            $post = [
                                "addForumTopic"=>"true",
                                "addForumTopic-bodyCode" => $sContentNew,
                                "addForumTopic-title" => $this->sTitle,
                                "addForumTopic-shortDescription" => $this->sShortDescription,
                                "addForumTopic-inputKeywords" => $sTagsArray,
                                "addForumTopic-image" => $this->sImage,
                                "addForumTopic-imageUploadAlt" => $this->sImageAlt,
                                "addForumTopic-imageUpload" => "",
                                "addForumTopic-coverImageUpload"=>'',
                                "addForumTopic-coverImage"=>'',
                                "addForumTopic-country"=>$this->sLanguage,
                                "addForumTopic-city"=>$CrawlerDataInformation->sCity,
                                "addForumTopic-additionalInformation"=>json_encode($additionalInformation),
                            ];

                            global $sSkyHubServerUri;

                            $sSkyHubFullURL = $sSkyHubServerUri. $crawlerIdentifiedCategory['category'];

                            $this->addJob(["skyhub_full_url"=>$sSkyHubFullURL,"user"=>$crawlerIdentifiedCategory["user"],"url original"=>$url, "time"=>$iIntervalMin, "post"=>$post,
                                "sTitle"=>$this->sTitle,"sContent"=>$this->sContent,"sAuthor"=>$this->sAuthor,"sImage"=>$this->sImage,"sDate"=>$this->sDate,"sTime"=>$this->sTime,
                                "arrTags"=>$this->arrTags, "arrAdditionalInformation"=>$additionalInformation, "sImageAlt"=>$this->sImageAlt, "dateTime" => $dateTime, "today"=>$today, "interval"=>$interval,
                                "iIntervalMin"=>$iIntervalMin]);
                            $bJobAdded=true;

                            $bPrintAggregatedData=true;
                        } else
                        {
                            echo("<b>Rejected NO TAG found ".$url." </b><br/>");
                            $bPrintAggregatedData=true;
                        }
                    }

            } else
            {
                $bPrintAggregatedData=true;
            }

            if ($bPrintAggregatedData)
            {
                var_dump('url:                '.$url);
                var_dump('title:              '.$this->sTitle);
                var_dump('content:            '.$this->sContent);
                var_dump('short-Description:  '.$this->sShortDescription);
                var_dump('author:             '.$this->sAuthor);
                var_dump('date:               '.$this->sDate . '     time:     ' . $this->sTime);
                var_dump($today->format('Y-m-d H:i:s'));
                var_dump($dateTime->format('Y-m-d H:i:s'));


                echo 'tags:';
                var_dump($this->arrTags);
                echo '<img alt="' . $this->sImageAlt . '" src="' . $this->sImage . '" > <br/>';
                var_dump('Days      :'.$interval->days);
            }

            if (!$bJobAdded)
                $this->addJob(null);
        }
    }

    protected  function jobFinished()
    {

        $bOK=true;
        while ($bOK==true)
        {
            $bOK=false;
            for ($index=0; $index < count($this->arrJobsResults)-1; $index++)
            {
                $iTime1= $this->arrJobsResults[$index]['time'];
                $iTime2= $this->arrJobsResults[$index+1]['time'];
                if ($iTime1 < $iTime2)
                {
                    $aux = $this->arrJobsResults[$index];
                    $this->arrJobsResults[$index] = $this->arrJobsResults[$index+1];
                    $this->arrJobsResults[$index+1] = $aux;
                    $index--;
                    $bOK=true;
                }
            }
        }

        foreach ($this->arrJobsResults as $job)
        {
            $url = $job["url original"];
            $sSkyHubFullURL = $job['skyhub_full_url'];
            $post = $job['post'];
            $sTitle = $job['sTitle'];
            $user = $job['user'];

            if ($this->CrawlerVisitedSites->checkUploadedSite($url, $sTitle) != -1)
            {
                echo("<b>Rejected for uploaded already: ".$url." </b><br/>");
            }
            else
            {
                if (isset($user )) $this->User->login($user );
                else $this->User->login('muflonel2000');

                //echo 'SENDING NEWS POST TO ### '.$sSkyHubFullURL.' ### <br/>';
                //echo 'POST ### '; print_r($post); echo ' ### <br/>';
                if ($sSkyHubFullURL != '') $response = $this->User->cURLSendPost($sSkyHubFullURL, $post);
                else $response = null;

                //echo 'RESPONSE_STR ### '; print_r($response); echo ' ###<br/>';
                $JSONResponse = json_decode($response, true);
                //echo 'RESPONSE ### '; print_r($JSONResponse); echo ' ###<br/>';

                if (($JSONResponse != null) && ($JSONResponse['result'] == true)) {
                    echo("<b>Success JOB PUBLISHED: " . $url . " </b><br/>");
                    $this->CrawlerVisitedSites->addUploadedSite($url, $sTitle);
                } else {

                    $sContent = $job['sContent'];
                    $sAuthor = $job['sAuthor'];
                    $sImage = $job['sImage'];
                    $sDate = $job['sDate'];
                    $sTime = $job['sTime'];
                    $sImageAlt = $job['sImageAlt'];
                    $arrTags = $job['arrTags'];
                    $dateTime = $job['dateTime'];
                    $today = $job['today'];
                    $interval = $job['interval'];
                    $arrAdditionalInformation = $job['arrAdditionalInformation'];

                    echo 'There <b>was a problem about publishing</b>: <b>' . ($JSONResponse != null ? $JSONResponse['message'] : '') . '</b> </br>';
                    echo $response . '<br/>';
                    echo '<p>' . $url . '</p>';
                    echo '<p>' . $sTitle . '</p>';
                    echo '<p>' . $sContent . '</p>';
                    echo '<p>' . $sAuthor . '</p>';
                    echo '<p>' . $sDate . ' ' . $sTime . '</p>';
                    echo '<p>' . $dateTime->format('Y-m-d H:i:s') . '</p>';
                    echo '<p>' . $today->format('Y-m-d H:i:s') . '</p>';
                    if (count($arrTags) == 0)
                        echo("<b>NO tags </b><br/>");
                    else
                        echo '<p>' . print_r($arrTags) . '</p>';

                    if (count($arrAdditionalInformation) == 0)
                        echo ("<b>NO Additional Information</b><br/>");
                    else
                        echo '<p>' . print_r($arrAdditionalInformation) . '</p>';

                    echo '<img alt="' . $sImageAlt . '" src="' . $sImage . '" > ';
                    echo '<p>Days difference ' . $interval->days . '</p><br/>';
                }
            }
        }

        $this->arrJobsResults = array();

        echo '****************JOB FINISHED**********************<br/>';
        echo '****************JOB FINISHED**********************<br/>';
        echo '****************JOB FINISHED**********************<br/>';
    }

    protected function extractDefaultCrawledData($DOM)
    {
        //TITLE
        $this->nodeTitle = $this->checkElement($DOM, [$DOM], $this->CrawlerDataInformationDefault->getCrawlerExtraction('title'));
        if ($this->nodeTitle != null)  $this->sTitle = $this->extractValueFromDOMElement($this->nodeTitle, $bArray = false);

        //TITLE OG-TITLE - facebook
        $this->nodeTitle = $this->checkElement($DOM, [$DOM], $this->CrawlerDataInformationDefault->getCrawlerExtraction('title-og'));
        if ($this->nodeTitle != null)
        {
            $sTitle = $this->extractValueFromDOMElement($this->nodeTitle, $bArray = false);
            if (strlen($sTitle) > 2) $this->sTitle = $sTitle;
        }

        //Short Description
        $this->nodeShortDescription = $this->checkElement($DOM, [$DOM], $this->CrawlerDataInformationDefault->getCrawlerExtraction('meta-short-description'));
        if ($this->nodeShortDescription != null) $this->sShortDescription = $this->extractValueFromDOMElement($this->nodeShortDescription, $bArray = false);

        //Short Description - OG
        $this->nodeShortDescription = $this->checkElement($DOM, [$DOM], $this->CrawlerDataInformationDefault->getCrawlerExtraction('short-description-og'));
        if ($this->nodeShortDescription != null)
        {
            $sShortDescription = $this->extractValueFromDOMElement($this->nodeShortDescription, $bArray = false);
            if (strlen($sShortDescription) > 2)
                $this->sShortDescription = $sShortDescription;
        }

        $this->nodeTags = $this->checkElement($DOM, [$DOM], $this->CrawlerDataInformationDefault->getCrawlerExtraction('meta-keywords'));
        if ($this->nodeTags != null) $this->arrTags = explode(',',$this->extractValueFromDOMElement($this->nodeTags, $bArray =  false));

        $this->nodeAuthor = $this->checkElement($DOM, [$DOM], $this->CrawlerDataInformationDefault->getCrawlerExtraction('meta-author'));
        if ($this->nodeAuthor != null) $this->sAuthor = $this->extractValueFromDOMElement($this->nodeAuthor, $bArray =  false);

        //HTML LANGUAGE
        $this->nodeLanguage = $this->checkElement($DOM, [$DOM], $this->CrawlerDataInformationDefault->getCrawlerExtraction('html-language'));
        if ($this->nodeLanguage != null) $this->sLanguage = $this->extractValueFromDOMElement($this->nodeLanguage, $bArray = false);

        //META LANGUAGE
        $this->nodeLanguage = $this->checkElement($DOM, [$DOM], $this->CrawlerDataInformationDefault->getCrawlerExtraction('meta-language'));
        if ($this->nodeLanguage != null){
            $sLanguage = $this->extractValueFromDOMElement($this->nodeLanguage, $bArray = false);
            if (strlen($sLanguage) > 1)
                $this->sLanguage = $sLanguage;
        }

        //META LANGUAGE 2
        $this->nodeLanguage = $this->checkElement($DOM, [$DOM], $this->CrawlerDataInformationDefault->getCrawlerExtraction('meta-language2'));
        if ($this->nodeLanguage != null){
            $sLanguage = $this->extractValueFromDOMElement($this->nodeLanguage, $bArray = false);
            if (strlen($sLanguage) > 1)
                $this->sLanguage = $sLanguage;
        }

        //LANGUAGE OG
        $this->nodeLanguage = $this->checkElement($DOM, [$DOM], $this->CrawlerDataInformationDefault->getCrawlerExtraction('language-og'));
        if ($this->nodeLanguage != null)
        {
            $sLanguage = $this->extractValueFromDOMElement($this->nodeLanguage, $bArray = false);
            if (strlen($sLanguage) > 1)
                $this->sLanguage = $sLanguage;
        }

        //IMAGE OG
        $this->nodeImage = $this->checkElement($DOM, [$DOM], $this->CrawlerDataInformationDefault->getCrawlerExtraction('image-og'));
        if ($this->nodeImage != null) $this->sImage = $this->extractValueFromDOMElement($this->nodeImage, $bArray = false);

        //TAGS OG
        $this->nodeTags = $this->checkElement($DOM, [$DOM], $this->CrawlerDataInformationDefault->getCrawlerExtraction('tags-og'));
        if ($this->nodeTags != null) $this->arrTags = $this->extractValueFromDOMElement($this->nodeTags, $bArray = true);

        /*$this->nodeContent = $this->checkElement($DOM, [$DOM] , $this->CrawlerInfo->arrNews['content']);
        $this->nodeDate = $this->checkElement($DOM, [$DOM] , $this->CrawlerInfo->arrNews['date']);
        $this->nodeTime = $this->checkElement($DOM, [$DOM] , $this->CrawlerInfo->arrNews['time']);
        $this->nodeImageAlt = $this->checkElement($DOM, [$DOM] , $this->CrawlerInfo->arrNews['image']['image-preview-alt']);*/
    }

    protected function extractPersonalizedCrawledData($CrawlerDataInformation, $DOM, $nodeContainer)
    {
        //Get FIRST IMAGE from the Container
        if ($this->sImage == '')
        {
            $this->nodeImage = $this->checkElement($DOM, $nodeContainer, $this->CrawlerDataInformationDefault->getCrawlerExtraction('image-first-container','image-preview'));

            if ($this->nodeImage == null)
                $this->nodeImage = $this->checkElement($DOM, [$DOM], $this->CrawlerDataInformationDefault->getCrawlerExtraction('image-first-container','image-preview'));

            if ($this->nodeImage != null)
            {
                $sImage = $this->extractValueFromDOMElement($this->nodeImage, $bArray = false);

                $this->nodeImageAlt = $this->checkElement($DOM, $nodeContainer, $this->CrawlerDataInformationDefault->getCrawlerExtraction('image-first-container','image-preview-alt'));
                $sImageAlt = $this->extractValueFromDOMElement($this->nodeImageAlt, $bArray = false);

                if (($this->sImage == '')&&(strlen($sImage) > 3))
                {
                    $this->sImage = $sImage;
                    $this->sImageAlt = $sImageAlt;
                }
            }
        }

        if ($CrawlerDataInformation->getCrawlerExtraction('short-description','default') !== null)
        {
            if ($CrawlerDataInformation->getCrawlerExtraction('short-description','default') === false)
                $this->sShortDescription = '';
        }

        if ($CrawlerDataInformation->getCrawlerExtraction('title','default') !== null)
        {
            if ($CrawlerDataInformation->getCrawlerExtraction('title','default') === false)
                $this->sTitle= '';
        }

        $this->nodeTitle = $this->checkElement($DOM, $nodeContainer , $CrawlerDataInformation->getCrawlerExtraction('title'));
        $this->nodeShortDescription = $this->checkElement($DOM, $nodeContainer , $CrawlerDataInformation->getCrawlerExtraction('short-description'));
        $this->nodeContent = $this->checkElement($DOM, $nodeContainer , $CrawlerDataInformation->getCrawlerExtraction('content'));

        $this->nodeAuthor = $this->checkElement($DOM, $nodeContainer , $CrawlerDataInformation->getCrawlerExtraction('author'));

        if ($CrawlerDataInformation->getCrawlerExtraction('date') != null)
            $this->nodeDate = $this->checkElement($DOM, $nodeContainer , $CrawlerDataInformation->getCrawlerExtraction('date'));
        else
            $this->nodeDate = $nodeContainer ;

        if ($CrawlerDataInformation->getCrawlerExtraction('time') != null)
            $this->nodeTime = $this->checkElement($DOM, $nodeContainer , $CrawlerDataInformation->getCrawlerExtraction('time'));
        else
            $this->nodeTime = $nodeContainer ;

        $this->nodeImage = $this->checkElement($DOM, $nodeContainer , $CrawlerDataInformation->getCrawlerExtraction('image','image-preview'));
        $this->nodeImageAlt = $this->checkElement($DOM, $nodeContainer , $CrawlerDataInformation->getCrawlerExtraction('image','image-preview-alt'));
        $this->nodeTags = $this->checkElement($DOM, $nodeContainer , $CrawlerDataInformation->getCrawlerExtraction('tags'));

        $sTitle = $this->extractValueFromDOMElement($this->nodeTitle, $bArray = false);
        $sContent = $this->extractValueFromDOMElement($this->nodeContent, $bArray = false, $bReturnOnlyText = $CrawlerDataInformation->getCrawlerExtraction('content','remove-links') );
        $sShortDescription = $this->extractValueFromDOMElement($this->nodeShortDescription, $bArray = false);

        $sAuthor  = $this->extractValueFromDOMElement($this->nodeAuthor, $bArray = false);
        $sDate = $this->extractValueFromDOMElement($this->nodeDate,$bArray = false);
        $sTime = $this->extractValueFromDOMElement($this->nodeTime,$bArray = false);
        $sImage = $this->extractValueFromDOMElement($this->nodeImage,$bArray = false);
        $sImageAlt = $this->extractValueFromDOMElement($this->nodeImageAlt, $bArray =  false);
        $arrTags = $this->extractValueFromDOMElement($this->nodeTags,$bArray = true);

        /*REPLACING THE DEFAULT EXTRACTED DATA with PERSONALIZED CRAWLED DATA */
        if (($sTitle != null) && (strlen($sTitle) > 4)) $this->sTitle = $sTitle;
        if (($sShortDescription != null) && (strlen($sShortDescription) > 4)) $this->sShortDescription = $sShortDescription;
        if (($sAuthor != null) && (strlen($sAuthor) > 2)) $this->sAuthor  = $sAuthor ;
        if (($sAuthor != null) && (strlen($sAuthor) > 2)) $this->sAuthor  = $sAuthor ;
        if (($sImage != null) && (strlen($sImage) > 4)) $this->sImage = $sImage;
        if (($arrTags != null) && (count($arrTags) > 0)) $this->arrTags = $arrTags;

        if (($sContent != null) && (strlen($sContent) > 0)) $this->sContent = $sContent;
        if (($sDate != null) && (strlen($sDate) > 0)) $this->sDate = $sDate;
        if (($sTime != null) && (strlen($sTime) > 0)) $this->sTime = $sTime;
        if (($sImageAlt != null) && (strlen($sImageAlt) > 0)) $this->sImageAlt = $sImageAlt;
    }

    protected function initializeNodes()
    {
        $this->nodeTitle = null; $nodeContent = null; $nodeAuthor = null; $nodeDate = null; $nodeTime = null; $nodeShortDescription = null;
        $this->nodeImage = null; $nodeImageAlt = null; $nodeTags = null;
    }

    protected function initializeDataFound()
    {
        $this->sTitle = ''; $this->sContent = ''; $this->sAuthor = ''; $this->sDate = '';  $this->sTime='';
        $this->sImage = ''; $this->sImageAlt = ''; $this->arrTags = []; $this->sShortDescription = ''; $this->sLanguage = '';
    }

}