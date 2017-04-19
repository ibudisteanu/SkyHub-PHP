<?php

    echo 'Starting Crawler ...  <br/>';

    require_once __DIR__.'/Crawler/CrawlerProcessor.php';
    echo 'Crawler File included ... <br/>';
    require_once __DIR__ . '/Crawler/CrawlerProcessors/CrawlerSitemap.php';
    echo 'Crawler News File included ... <br/>';

    require_once __DIR__.'/crawlers_info/skyhub_sitemap/SkyHub.me_Info.php';

    $MyCrawler = new CrawlerSitemap();
    $CrawlerInfo = new CrawlerData_SkyHub_info();
    $MyCrawler->setCrawlerInfo($CrawlerInfo);
    echo 'Crawler Info set ... <br/> <br/>';

    echo 'Starting Sitemap Crawler <br/> <br/>';
    $MyCrawler->User->login("muflonel2000");

    $MyCrawler->startCrawl(1000);
    echo 'Crawler finished ... <br/>';

?>