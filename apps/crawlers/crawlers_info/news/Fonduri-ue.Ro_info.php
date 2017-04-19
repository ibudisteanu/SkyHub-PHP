<?php

require_once __DIR__.'/../CrawlerDataInfo.php';

class CrawlerData_FonduriueRo_info extends CrawlerDataInfo
{
    public $sWebsite = 'http://www.fonduri-ue.ro/';
    public $arrWebsite = array('fonduri-ue.ro','www.fonduri-ue.ro');
    public $sCity = 'Bucharest';

    public $arrPages = [
        ["link"=>"http://www.fonduri-ue.ro/presa/arhiva-noutati/2169-inca-doua-conditionalitati-ex-ante-indeplinite-de-romania","link"],
        ["link"=>"http://www.fonduri-ue.ro","link"]
    ];

    public $arrUnFollowPages = [ ];

    public $arrCrawlerExtraction =
        [
            "container" =>
                [
                    "tagName"=>"div",
                    "tagClass" => "t3-content"
                ],
            "title"=>
                [
                    "tagName"=>"h1",
                    "tagClass"=>"article-title"
                ]
            ,
            "content"=>
                [
                    "tagName"=>"section",
                    "tagClass"=>"article-content clearfix",
                    "remove-links"=>true,
                ]
            ,
            "date"=>
                [
                    "find-element-first"=>
                        [
                            "tagName"=>"dd",
                            "tagClass"=>"published hasTooltip"
                        ],

                    "tagName"=>"time",
                ]
            ,
        ];
}