<?php

require_once __DIR__.'/../CrawlerDataInfo.php';

class CrawlerData_StiriAgricoleRo_info extends CrawlerDataInfo
{
    public $sWebsite = 'https://www.stiriagricole.ro/';
    public $sWebsiteName = 'Stiri Agricole';
    public $sCity = 'Bucharest';

    public $arrWebsite = array('stiriagricole.ro','www.stiriagricole.ro');

    public $arrPages = [
        ["link"=>"https://www.stiriagricole.ro/pndr-conditii-de-eligibilitate-pentru-dezvoltarea-fermelor-mici-45037.html","link"],
        ["link"=>"https://stiriagricole.ro/","link"]
    ];

    public $arrUnFollowPages = [ ];

    public $arrCrawlerExtraction =
        [
            "container" =>
                [
                    "tagId" => "content"
                ]
            ,
            "short-description" =>
                [
                    "default"=>false,
                ],
            "content"=>
                [
                    "tagName"=>"div",
                    "tagClass"=>"pf-content",
                    "remove-links"=>true,
                ]
            ,
            "author"=>
                [
                    "find-element-first"=>
                        [
                            "tagName"=>"a",
                        ],
                    'tagAttribute' => ['tagAttributeName' => 'rel', 'tagAttributeValue' => 'author' ],
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
                    "tagName"=>"span",
                    "tagClass" => "date",
                ]
        ];
}