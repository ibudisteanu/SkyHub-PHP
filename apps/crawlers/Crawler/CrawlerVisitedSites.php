<?php

class CrawlerVisitedSites
{

    public $sFileName = __DIR__."/../../../uploads/apps_res/crawler/crawler_data239945412345XXFF.txt";
    public $arrData = array("uploaded"=>[],"checked"=>[]);
    private $iCount = 0;
    private $iFlushCount = 1;
    //public $iFlushCount = 20;

    public function __construct()
    {
        $this->readFile();
    }

    public function readFile()
    {
        $myfile = fopen($this->sFileName, "r");

        if ($myfile == null) {
            $this->saveFile();
            return;
        }

        $sFileContent = fread($myfile,filesize($this->sFileName));

        $json = json_decode($sFileContent, true);
        if ($json != null)
            $this->arrData = $json;
        $this->iCount=0;

        fclose($myfile);
    }

    public function addUploadedSite($sLink, $sTitle, $bResult=false, $date=null)
    {
        if ($date == null) $date = new DateTime();
        $data = ["link"=>$sLink,"title"=>$sTitle,"result"=>$bResult,"date"=>$date->format('Y-m-d H:i:s')];

        array_push($this->arrData['uploaded'], $data);
        $this->iCount++;
        if ($this->iCount % $this->iFlushCount == 0)  $this->SaveFile();
    }

    public function addCheckedSite($sLink, $sTitle, $bResult=false, $date=null)
    {
        if ($date == null) $date = new DateTime();
        $data = ["link"=>$sLink,"title"=>$sTitle,"result"=>$bResult,"date"=>$date];

        array_push($this->arrData['checked'], $data->format('Y-m-d H:i:s'));
        $this->iCount++;
        if ($this->iCount % $this->iFlushCount == 0)  $this->SaveFile();
    }

    public function checkUploadedSite($sLink, $sTitle)
    {
        return $this->checkDataLink('uploaded',$sLink, $sTitle);
    }

    public function checkCheckedSite($sLink, $sTitle)
    {
        return $this->checkDataLink('checked',$sLink, $sTitle);
    }

    protected  function checkDataLink($sDataName, $sLink, $sTitle)
    {
        $arr = $this->arrData[$sDataName];
        for ($index=0; $index < count($arr); $index++ )
        {
            $item = $arr[$index];
            if ((strtolower($item['link']) == strtolower($sLink)) || (strpos(strtolower($sLink),strtolower($item['link']) !== FALSE) || (strpos(strtolower($item['link']), strtolower($sLink)) !== FALSE)))
                return $index;

            if ((isset($item['title'])) && ($sTitle != ''))
            if ((strtolower($item['title']) == strtolower($sTitle)) || (strpos(strtolower($sTitle),strtolower($item['title']) !== FALSE) || (strpos(strtolower($item['title']), strtolower($sTitle)) !== FALSE)))
                return $index;
        }
        return -1;
    }

    public function saveFile()
    {
        $myfile = fopen($this->sFileName, "w") or die("Unable to open file!");

        $json = json_encode($this->arrData);

        fwrite($myfile, $json);

        fclose($myfile);
    }

}