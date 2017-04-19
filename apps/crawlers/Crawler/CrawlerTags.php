<?php

class CrawlerTags
{

    public $sTagFileName = __DIR__."/../../../uploads/apps_res/crawler/crawler_tags2333xxsSS2.txt";

    public function __construct()
    {
        $this->readFile();
    }

    public function identifyCategoryToPublishFromProcessTags($arrFoundTags)
    {
        if ($arrFoundTags == null ) return false;
        if (!is_array($arrFoundTags)) return false;

        foreach ($arrFoundTags as $sFoundTag)
        {
            //Each element fro mTags
            $result = $this->findCrawlerCategoryFromTag($sFoundTag);
            if ($result != null) return $result;
        }

        //Nothing so far, throw the Crawler to Publish in the General Section
        $resultAny = $this->findCrawlerCategoryFromTag('general-news');
        if ($resultAny != null) return $resultAny;

        return null;
    }

    public function readFile()
    {
        $myfile = fopen($this->sTagFileName, "r");

        if ($myfile == null) {
            echo '<b>'.$this->sTagFileName.'</b>';
            return;
        }

        $sFileContent = fread($myfile,filesize($this->sTagFileName));

        $json = json_decode($sFileContent, true);
        if ($json != null)
            $this->arrCrawlerCategoriesForPublishing = $json;
        else
            echo '<b> NO VALID CATEGORIES - TAGS FOUND </b>';

        fclose($myfile);
    }

    protected function findCrawlerCategoryFromTag($sTagToSearch)
    {
        foreach ($this->arrCrawlerCategoriesForPublishing as $categoryData)
        {
            $tags = []; $sUser = ''; $sForum =''; $sCategory = '';
            if (isset($categoryData['tags']))  $tags = $categoryData['tags'];
            if (isset($categoryData['user'])) $sUser = $categoryData['user'];
            if (isset($categoryData['forum'])) $sForum = $categoryData['forum'];
            if (isset($categoryData['category'])) $sCategory = $categoryData['category'];

            foreach ($tags as $tag)
                if (strtolower($sTagToSearch) == strtolower($tag))
                {
                    return $categoryData;
                }
        }
        return null;
    }

    public $arrCrawlerCategoriesForPublishing = //EXAMPLES, the tags are read from the $sTagFileName FILE
        [
            [
                'tags'=>['klaus iohannis','iohannis'],
                'user'=>'muflonel2000',
                'forum'=>'forum/People/Politcians/forum-Klaus-Iohannis/57ddb31b08e581b4110000f6',
                'category'=>'api/topic/post/add/57de09b308e581b41100031e',
            ],
            [
                'tags'=>['ponta','victor ponta','monta'],
                'user'=>'muflonel2000',
                'forum'=>'/forum/People/Politcians/forum-Victor-Ponta/57ddafcb08e581b411000094',
                'category'=>'api/topic/post/add/57de090c08e581b411000254',
            ],
            [
                'tags'=>['basescu','traian basescu','presedintele basescu'],
                'user'=>'muflonel2000',
                'forum'=>'/forum/People/Politcians/forum-traian-basescu/57ddad3a08e581a41100006c',
                'category'=>'api/topic/post/add/57ddbc7a08e581bc110000dd',
            ],
            [
                'tags'=>['udrea','elena udrea','blonda de la cotroceni'],
                'user'=>'muflonel2000',
                'forum'=>'forum/People/Politcians/forum-Elena-Udrea/57ddae0308e581a4110000e5',
                'category'=>'api/topic/post/add/57de072c08e581ac1100082e',
            ],
            [
                'tags'=>['udrea','elena udrea','blonda de la cotroceni'],
                'user'=>'muflonel2000',
                'forum'=>'forum/People/Politcians/forum-Elena-Udrea/57ddae0308e581a4110000e5',
                'category'=>'api/topic/post/add/57de072c08e581ac1100082e',
            ],
            [
                'tags'=>['tariceanu','calin tariceanu','calin popescu tariceanu','popescu tariceanu'],
                'user'=>'muflonel2000',
                'forum'=>'forum/People/Politcians/forum-Calin-Popescu-Tariceanu/57ddb3bf08e581cc110001fd',
                'category'=>'api/topic/post/add/57de0a3708e581b4110003ca',
            ],
            [
                'tags'=>['firea','gabriela firea','firea PSD'],
                'user'=>'muflonel2000',
                'forum'=>'forum/People/Politcians/forum-Gabriela-Firea/57ddc71708e581ac110000c0',
                'category'=>'api/topic/post/add/57de0b0408e581b4110004bd',
            ],
            [
                'tags'=>['firea','gabriela firea','firea PSD'],
                'user'=>'muflonel2000',
                'forum'=>'forum/People/Politcians/forum-Gabriela-Firea/57ddc71708e581ac110000c0',
                'category'=>'api/topic/post/add/57de0b0408e581b4110004bd',
            ],
            [
                'tags'=>['gigi','gigi becali','becali','george becali'],
                'user'=>'muflonel2000',
                'forum'=>'forum/People/Politcians/Povetele-lu-Becali/57ddd90b08e581cc1100039c',
                'category'=>'api/topic/post/add/57de0b9908e581b411000569',
            ],
            [
                'tags'=>['kovesi','DNA','codruta kovesi','laura codruta kovesi','codruta'],
                'user'=>'muflonel2000',
                'forum'=>'/forum/People/Politcians/Codruta-Kovesi/57e4bd8c08e5815412000668',
                'category'=>'api/topic/post/add/57e4c06808e5816809000ad0',
            ],
            [
                'tags'=>['blaga','Vasile Blaga','blaga','PNL','lider PNL'],
                'user'=>'muflonel2000',
                'forum'=>'/forum/People/Politcians/La-Blaga/57e4be7208e58154120007d7',
                'category'=>'api/topic/post/add/57e4c1ea08e5815412000adb',
            ],
            [
                'tags'=>['Liviu Dragnea','dragnea','dragnea PSD','PSD','dragnea liviu'],
                'user'=>'muflonel2000',
                'forum'=>'/forum/People/Politcians/La-Dragnea/57e4c73a08e5815412000d76',
                'category'=>'api/topic/post/add/57e4c7a308e5815412000e0c',
            ],
        ];

}