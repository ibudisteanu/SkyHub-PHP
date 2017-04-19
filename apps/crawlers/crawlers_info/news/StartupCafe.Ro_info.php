<?php

require_once __DIR__.'/../CrawlerDataInfo.php';

class CrawlerData_StartupCafeRo_info extends CrawlerDataInfo
{
    public $sWebsite = 'http://startupcafe.ro/';
    public $sWebsiteName = 'Startup Cafe';
    public $sCity = 'Bucharest';

    public $arrWebsite = array('startupcafe.ro');

    public $arrPages = [
        ["link"=>"http://www.startupcafe.ro/stiri-finantari-21714844-start-nation-startup-fonduri-firme.htm","link"],
        ["link"=>"https://start-up.ro/","link"]
    ];

    public $arrUnFollowPages = [ ];

    public $arrCrawlerExtraction =
        [
            "container" =>
                [
                    "tagName"=>"div",
                    "tagClass" => "article-content-row"
                ]
            ,
            "short-description" =>
                [
                    "default"=>false,
                ],
            "content"=>
                [
                    "tagName"=>"div",
                    "tagClass"=>"article-inside-text",
                    "remove-links"=>true,
                ]
            ,
            "author"=>
                [
                    "find-element-first"=>
                        [
                            "tagName"=>"span",
                            "tagClass"=>"author"
                        ],
                    "tagName"=>"a",
                    "attribute_return"=>"link",
                ]
            ,
            "date"=>
                [
                    "tagName"=>"span",
                    "tagClass" => "date",
                ]
            ,
            "time"=>
                [
                    "tagName"=>"time",
                ]
            ,
            "tags"=>
                [
                    "find-element-first"=>
                        [
                            "tagName"=>"a",
                        ],
                    'tagAttribute' => ['tagAttributeName' => 'rel', 'tagAttributeValue' => 'tag' ],
                ]
        ];
}