<?php

    echo 'Starting Crawler ...  <br/>';

    require_once __DIR__.'/Crawler/CrawlerProcessor.php';
    echo 'Crawler File included ... <br/>';
    require_once __DIR__ . '/Crawler/CrawlerProcessors/CrawlerNews.php';
    echo 'Crawler News File included ... <br/>';

    $arrayCrawlerInfo =
        ['StiriPeSurse.Ro_info','Antena3.Ro_info','Start-up.Ro_info','Fonduri-ue.Ro_info','StartupCafe.Ro_info','Hotnews.Ro_info',
            'Finantare.Ro_info','Fonduri-structurale.Ro_info','StiriAgricole.Ro_info'];
    foreach ($arrayCrawlerInfo as $Info)
    {
        require_once __DIR__.'/crawlers_info/news/'.$Info.'.php';
        echo 'Crawler File: '.$Info.' included ... <br/>';
    }

    $MyCrawler = new CrawlerNews();

    $arrCrawlersInfo = [];
//    array_push($arrCrawlersInfo, new CrawlerData_StiriPeSurseRo_info());
    //array_push($arrCrawlersInfo, new CrawlerData_Antena3Ro_info());
//    array_push($arrCrawlersInfo, new CrawlerData_StartupRo_info());
    //array_push($arrCrawlersInfo, new CrawlerData_FonduriueRo_info());
    //array_push($arrCrawlersInfo, new CrawlerData_StartupCafeRo_info());
//    array_push($arrCrawlersInfo, new CrawlerData_HotnewsRo_info());
    array_push($arrCrawlersInfo, new CrawlerData_FinantareRo_info());
//    array_push($arrCrawlersInfo, new CrawlerData_FonduriStructuraleRo_info());
//    array_push($arrCrawlersInfo, new CrawlerData_StiriAgricoleRo_info());

    $MyCrawler->setCrawlerDataInfo($arrCrawlersInfo);
    $MyCrawler->startCrawl(100);

    //$MyCrawler->crawl_page("http://hobodave.com", 2);

?>